<?php
/**
 * This file is part of the PHPOrchestra\CMSBundle.
 *
 * @author Nicolas ANNE <nicolas.anne@businessdecision.com>
 */

namespace PHPOrchestra\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlockExampleController extends Controller
{
    /**
     * Render a block
     * 
     * @param 
     */
    public function show0Action()
    {
        return $this->render('PHPOrchestraCMSBundle:BlockExample:show0.html.twig', array());
    }

    /**
     * Render a block
     * 
     * @param 
     */
    public function form0Action($prefix)
    {
        return $this->render('PHPOrchestraCMSBundle:BlockExample:form0.html.twig', array('prefix' => $prefix));
    }
    
    /**
     * Render a block
     * 
     * @param 
     */
    public function show1Action($title)
    {
        return $this->render('PHPOrchestraCMSBundle:BlockExample:show1.html.twig', array());
    }
    /**
     * Render a block
     * 
     * @param 
     */
    public function form1Action($prefix)
    {
        return $this->render('PHPOrchestraCMSBundle:BlockExample:form1.html.twig', array('prefix' => $prefix));
    }
}
