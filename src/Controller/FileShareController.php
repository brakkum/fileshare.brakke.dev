<?php

namespace App\Controller;

use App\Entity\SharedFile;
use App\Repository\SharedFileRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class FileShareController extends AbstractController
{
    private $logger;
    private $params;

    public function __construct(LoggerInterface $logger, ParameterBagInterface $params)
    {
        $this->logger = $logger;
        $this->params = $params;
    }

    /**
     * @Route("/file/share/{hash}", name="file_share")
     * @param $hash
     * @return Response
     */
    public function share($hash)
    {
        /** @var SharedFileRepository $repo */
        $repo = $this->getDoctrine()->getRepository(SharedFile::class);
        $shared_file = $repo->findOneBy([
            "hash_of_file_contents" => $hash,
//            "has_been_shared" => false TODO:uncomment
        ]);

        if (!$shared_file) {
            return $this->render('file_share/no_file.html.twig', [
                "hash" => $hash
            ]);
        }

        $shared_file->setHasBeenShared(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($shared_file);
        $em->flush();

        $link = $this::getLinkstripLink($shared_file);
        if ($link->success) {
            $link_url = $link->url;
        } else {
            $link_url = $this->getDownloadLinkForFile($shared_file);
        }

        return $this->render('file_share/share.html.twig', [
            'link_url' => $link_url,
        ]);
    }

    /**
     * @Route("/file/download/{hash}", name="file_download")
     * @param $hash
     * @return Response
     */
    public function download_landing($hash)
    {
        /** @var SharedFileRepository $repo */
        $repo = $this->getDoctrine()->getRepository(SharedFile::class);
        $shared_file = $repo->findOneBy([
            "hash_of_file_contents" => $hash
        ]);

        if (!$shared_file || $shared_file->getNumberOfDownloads() >= $shared_file->getAllowedDownloads()) {
            return $this->render('file_share/no_file.html.twig', [
                "hash" => $hash
            ]);
        }

        $has_custom_secret = !password_verify("", $shared_file->getPrivateKey());

        return $this->render('file_share/download_landing.html.twig', [
            "has_custom_secret" => $has_custom_secret
        ]);
    }

    /**
     * @Route(
     *     "/file/get_file/{hash}/{secret}",
     *     name="file_download_with_secret",
     *     methods={"POST"},
     *     defaults={"secret"=""}
     * )
     * @Route(
     *     "/file/get_file/{hash}/",
     *     name="file_download_no_secret",
     *     methods={"POST"},
     * )
     * @param $hash
     * @param $secret
     * @return Response
     */
    public function download($hash, $secret = "")
    {
        /** @var SharedFileRepository $repo */
        $repo = $this->getDoctrine()->getRepository(SharedFile::class);
        $shared_file = $repo->findOneBy([
            "hash_of_file_contents" => $hash
        ]);
        $this->logger->alert(print_r($shared_file, true));

        if (!$shared_file) {
            return JsonResponse::fromJsonString(new JsonResponse([
                "success" => false,
                "message" => "No such file exists"
            ]));
        }

        if (!password_verify($secret, $shared_file->getPrivateKey())) {
            return JsonResponse::fromJsonString(new JsonResponse([
                "success" => false,
                "message" => "Bad secret"
            ]));
        }

        if ($shared_file->getNumberOfDownloads() >= $shared_file->getAllowedDownloads()) {
            return JsonResponse::fromJsonString(new JsonResponse([
                "success" => false,
                "message" => "Max downloads exceeded"
            ]));
        }

        $shared_file->setNumberOfDownloads($shared_file->getNumberOfDownloads() + 1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($shared_file);
        $em->flush();

        $file_path = $this->params->get('project_dir').UPLOAD_DIRECTORY.$shared_file->getHashOfFileContents();
        $file = file_get_contents($file_path);
        $file = decrypt($file, $secret);

        // return file
        $response = new Response($file);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $shared_file->getName()
        );
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

    public function getLinkstripLink(SharedFile $file)
    {
        $new_link_url = "https://linkst.rip/api/newLink";
        $curl = curl_init();
        $new_link_url = sprintf("%s?%s", $new_link_url, http_build_query(["url" => $this->getDownloadLinkForFile($file)]));
        curl_setopt($curl, CURLOPT_URL, $new_link_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }

    public function getDownloadLinkForFile(SharedFile $file)
    {
        return URL."/file/download/".$file->getHashOfFileContents();
    }
}
