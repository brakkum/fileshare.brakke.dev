<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /**
     * @Route("/upload/error", name="upload_error")
     */
    public function upload_error()
    {
        return $this->render('error/upload_error.html.twig');
    }
}
