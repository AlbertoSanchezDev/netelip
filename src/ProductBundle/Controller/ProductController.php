<?php

namespace ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ProductBundle\Entity\Producto;
use Symfony\Component\HttpFoundation\Request;
use ProductBundle\Form\ProductoType;

class ProductController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository("ProductBundle:Producto")->findAll();
        return $this->render('ProductBundle:Default:index.html.twig', array(
            'products' => $products
        ));
    }
    public function addAction(Request $request){
        
        $producto = new Producto();
        $form = $this->createForm(new ProductoType($producto), $producto, array(
            'action' => $this->generateUrl('add_product'),
            'method' => "POST"
        ));
        
        if ($request->isMethod("POST")) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $productData = $form->getData();
                $em->persist($productData);
                $em->flush();
                $em->clear();
                
                return $this->redirect($this->generateUrl('homepage')); 
            }
        }
        
        return $this->render('ProductBundle:Default:add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $producto = $em->getRepository('ProductBundle:Producto')->find($id);
        if($producto){
            $form = $this->createForm(new ProductoType($producto), $producto, array(
                'action' => $this->generateUrl('edit_product', array('id' => $id)),
                'method' => "POST"
            ));
            
            if ($request->isMethod("POST")) {
                $form->bind($request);
                if ($form->isValid()) {
                    $em->merge($producto);
                    $em->flush();
                    $em->clear();
                    
                    return $this->redirect($this->generateUrl('homepage')); 
                }
            }
            
            return $this->render('ProductBundle:Default:edit.html.twig', [
                'id' => $id,
                'form' => $form->createView(),
            ]);
        }else{
            return $this->redirect($this->generateUrl('homepage')); 
        }
    }
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();
        $producto = $em->getRepository('ProductBundle:Producto')->find($id);
        if($producto){
            $em->remove($producto);
            $em->flush();
            $em->clear();
        }
        
        return $this->redirect($this->generateUrl('homepage')); 
    }
}
