<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends Controller
{
    /**
     * Lists all news .
     *
     * @Route("/", name="news_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $news = $this->getLatestNews();
        $newsImagesPath = $this->container->getParameter('news_images_path');
        return $this->render('news/index.html.twig', array(
            'news' => $news,
            'newsImagesPath' => $newsImagesPath
        ));
    }

    /**
     * Lists all current users news .
     *
     * @Route("/my_news", name="my_news")
     * @Method("GET")
     */
    public function myNewsAction()
    {
        $news = $this->getLatestNews($this->getUser());
        $newsImagesPath = $this->container->getParameter('news_images_path');
        return $this->render('news/index.html.twig', array(
            'news' => $news,
            'newsImagesPath' => $newsImagesPath
        ));
    }

    /**
     * Creates a new news entity.
     *
     * @Route("/news/add", name="news_add")
     * @Method({"GET", "POST"})
     */
    public function addAction(Request $request)
    {
        $news = new News();
        $form = $this->createForm('AppBundle\Form\NewsType', $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $news->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($news);
            $em->flush($news);

            return $this->redirectToRoute('news_show', array('id' => $news->getId()));
        }

        return $this->render('news/add.html.twig', array(
            'news' => $news,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a news .
     *
     * @Route("/news/details/{id}", name="news_show")
     * @Method("GET")
     */
    public function showAction(News $news)
    {
        $newsImagesPath = $this->container->getParameter('news_images_path');
        return $this->render('news/show.html.twig', array(
            'news' => $news,
            'newsImagesPath' => $newsImagesPath,
        ));
    }


    /**
     * Deletes a news entity.
     *
     * @Route("/news/delete/{id}", name="news_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, News $news)
    {
            if($news->getUser()->getId() == $this->getUser()->getId()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($news);
                $em->flush($news);
                $this->addFlash(
                    'error',
                    'news deleted successfully .'
                );
            }
            else {
                $this->addFlash(
                    'error',
                    'You do not have permission to delete that news.'
                );
            }
           return $this->redirectToRoute('news_index');

    }

     /**
     * Download news .
     *
     * @Route("/news/{id}/download", name="news_download")
    * @Method({"GET", "POST"})
     */
    public function downloadAction(Request $request, News $news)
    {
        $newsImagesPath = $this->container->getParameter('news_images_path');
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:News');
        $pdf = $this->container->get("white_october.tcpdf")->create();
        $repository->downloadPdf($news, $pdf,  $this->container->get('request')->server->get('DOCUMENT_ROOT'), $newsImagesPath);

        return $this->redirectToRoute('news_index');
    }

    /**
     * Rss news .
     *
     * @Route("/news/rss/", name="news_rss")
     * @Method({"GET"})
     */
    public function rssAction(Request $request)
    {
        $news = $this->getLatestNews();
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost();
        $newsImagesPath = $this->container->getParameter('news_images_path');
        $response = new Response($this->render('news/rss.xml.twig', array(
            'news' => $news,
            'baseUrl' => $baseUrl,
            'newsImagesPath' => $newsImagesPath
        )));
        $response->headers->set('Content-Type', 'xml');
        return $response;
    }

    public function getLatestNews($user  = null) {
        $em = $this->getDoctrine()->getManager();
        if($user) {
            $conditions = array('user' => $user->getId());
            return  $em->getRepository('AppBundle:News')->findBy($conditions, array('created'=> 'DESC'));
        }
        else {
            $conditions = array();
            return  $em->getRepository('AppBundle:News')->findBy($conditions, array('created'=> 'DESC'),$this->container->getParameter('page_limit') );
        }

    }

    /**
     * Creates a form to delete a news entity.
     *
     * @param News $news The news entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(News $news)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('news_delete', array('id' => $news->getId())))
            ->setMethod('GET')
            ->getForm()
        ;
    }
}
