<?php

namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class BlogController extends BaseController {
    
    /**
     * @Route("/blog", name="blog")
     */
    public function indexAction(Request $request) {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Blog')->getAllOrderedByPosition();
       
        return $this->render('blog/blog.html.twig', array(
            'posts' => $posts
        ));
    }
    
    /**
     * @Route("/blog/detail/{title}/{id}", name="blog_detail")
     */
    public function detailAction(Request $request, $title, $id) {
        $post = $this->getDoctrine()->getRepository('AppBundle:Blog')->find($id);
       
        $nextPost = $this->getDoctrine()->getRepository('AppBundle:Blog')->getByPosition($post->getPosition() + 1);
        $prevPost = $this->getDoctrine()->getRepository('AppBundle:Blog')->getByPosition($post->getPosition() - 1);
        
        
        return $this->render('blog/blog_detail.html.twig', array(
            'post' => $post,
            'nextPost' => $nextPost,
            'prevPost' => $prevPost
        ));
    }
    
  
}
