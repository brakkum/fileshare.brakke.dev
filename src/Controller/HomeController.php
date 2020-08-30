<?php

namespace App\Controller;

use App\Entity\SharedFile;
use App\Form\Type\SharedFileType;
use App\Utilities\Constants;
use App\Utilities\EncryptionTools;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $params;
    private $logger;

    public function __construct(ParameterBagInterface $params, LoggerInterface $logger)
    {
        $this->params = $params;
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="home")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $shared_file = new SharedFile();
        $shared_file_form = $this->createForm(SharedFileType::class, $shared_file);
        $shared_file_form->handleRequest($request);

        if ($shared_file_form->isSubmitted() && $shared_file_form->isValid()) {
            /** @var UploadedFile $upload */
            $upload = $shared_file_form->get('file')->getData();

            $upload_directory = $this->params->get('project_dir').Constants::UPLOAD_DIRECTORY;
            if (!is_dir($upload_directory)) {
                mkdir($upload_directory);
            }

            $free_disk_space = disk_free_space("/");
            $available_space = $free_disk_space - Constants::MINIMUM_FREE_SPACE;
            $upload_size = $upload->getSize();
            if ($available_space <= $upload_size * 2) {
                return $this->redirectToRoute("upload_error");
            }



            $file_name = $upload->getClientOriginalName();
            $shared_file->setName($file_name);
            if ($shared_file->getAllowedDownloads() <= 0) {
                $shared_file->setAllowedDownloads(1);
            }

            if (!$shared_file->getPrivateKey()) {
                $shared_file->setPrivateKey($this->params->get("default_key"));
            }
            $private_key = $shared_file->getPrivateKey();
            $shared_file->setPrivateKey(password_hash($private_key, PASSWORD_BCRYPT));

            $unencrypted_file_path = $upload_directory;
            $unencrypted_file_hash = hash("sha256", file_get_contents($upload->getPathname()));

            $upload->move(
                $unencrypted_file_path,
                $unencrypted_file_hash
            );

            $unencrypted_file_full_path = $unencrypted_file_path.$unencrypted_file_hash;
            $upload_contents = file_get_contents($unencrypted_file_full_path);
            $upload_contents = EncryptionTools::encrypt($upload_contents, $private_key);
            $encrypted_file_hash = hash("sha256", $upload_contents);
            $shared_file->setHashOfFileContents($encrypted_file_hash);
            $encrypted_file_full_path = $unencrypted_file_path.$encrypted_file_hash;
            file_put_contents($encrypted_file_full_path, $upload_contents);
            unlink($unencrypted_file_full_path);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($shared_file);
            $entityManager->flush();

            return $this->redirectToRoute('file_share', [
                "hash" => $shared_file->getHashOfFileContents()
            ]);
        }

        return $this->render('home/index.html.twig', [
            'form' => $shared_file_form->createView(),
        ]);
    }
}
