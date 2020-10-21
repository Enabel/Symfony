<?php

namespace App\Controller;

use App\Service\RolesHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage()
    {
        return $this->render('index/index.html.twig');
    }

    /**
     * @Route("/secure", name="secure")
     * @IsGranted("ROLE_USER")
     */
    public function secure()
    {
        return $this->render('index/secure.html.twig');
    }
}
