<?php

namespace App\Controller;

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
        return $this->render('index/index.html.twig', [
            'controller_name' => 'Homepage',
        ]);
    }

    /**
     * @Route("/secure", name="secure")
     * @IsGranted("ROLE_USER")
     */
    public function secure()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'Secure',
        ]);
    }

    /**
     * @Route("/admin", name="admin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function admin()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'Admin',
        ]);
    }
}
