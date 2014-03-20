<?php
/**
 * This file is part of the PHPOrchestra\CMSBundle.
 *
 * @author Nicolas Anne <nicolas.anne@businessdecision.com>
 */

namespace PHPOrchestra\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PHPOrchestra\CMSBundle\Classes\Area;
use PHPOrchestra\CMSBundle\Form\Type\TemplateType;
use PHPOrchestra\CMSBundle\Classes\DocumentLoader;

class TemplateController extends Controller
{

    
    /**
     * Cache containing blocks defined in external Nodes
     * 
     * @var Mandango\Group\EmbeddedGroup[]
     */
    private $externalBlocks = array();
    

    /**
     * Render Template
     * 
     * @param int $templateId
     * @return Response
     */
    public function showAction($templateId)
    { 
        $template = DocumentLoader::getDocument('Template', array('templateId' => (int)$templateId), $this->container->get('mandango'));
        $areas = $template->getAreas();
        $this->externalBlocks = array();
        
        if (is_array($areas))
            foreach ($areas as $area)
                $this->getExternalBlocks(new Area($area));

        return $this->render('PHPOrchestraCMSBundle:Template:show.html.twig', array('template' => $template, 'relatedNodes' => $this->externalBlocks));
    }
    
    
    /** 
     * Cache blocks from external Nodes referenced in an area
     * 
     * @param Area $area
     */
    private function getExternalBlocks(Area $area)
    {
        foreach ($area->getBlockReferences() as $blockReference)
           if ($blockReference['nodeId'] != 0 && !(isset($this->cacheRelatedNodes[$blockReference['nodeId']])))
               $this->getBlocksFromNode($blockReference['nodeId']);
            
        foreach ($area->getSubAreas() as $subArea)
            $this->getExternalBlocks($subArea);
    }
    
    
    /**
     * Cache blocks from specific Node
     * 
     * @param int $templateId
     */
    private function getBlocksFromNode($nodeId)
    {
        $node = DocumentLoader::getDocument('Node', array('nodeId' => $nodeId), $this->container->get('mandango'));
        $this->externalBlocks[$nodeId] = $nodes->getBlocks();
    }
	
    /**
     * 
     * Render the templates form
     * @param int $templateId
     * @param Request $request
     * 
     */
    public function formAction($templateId, Request $request)
    {
        $mandango = $this->container->get('mandango');       
        if($templateId != 0){
            $template = DocumentLoader::getDocument('Template', array('templateId' => (int)$templateId), $this->container->get('mandango'));
        }
        else{
            $template = $mandango->create('Model\PHPOrchestraCMSBundle\Template');
            $template->setTemplateId(time());
            $template->setSiteId(1);
            $template->setName('');
            $template->setVersion(1);
            $template->setLanguage('fr');
            $template->setStatus('Draft');
        }
        
        $form = $this->createForm(new TemplateType(), $template);
        $form->handleRequest($request);
        
        if ($form->isValid())
        {
            $template = $this->setBlocks($form->get('blocks')->getData(), $template);

            $template->save();
            
            return $this->redirect($this->generateUrl('php_orchestra_cms_templateform', array('templateId' => $template->getTemplateId())));
        }
        
        return $this->render('PHPOrchestraCMSBundle:Template:form.html.twig', array(
            'form' => $form->createView(),
        ));    
    }
    

    
    private function setBlocks($blocks, $template)
    {
        $blocks = json_decode($blocks, true);    
            
        if (is_array($blocks))
        {
            $mandango = $this->container->get('mandango'); 
                  
            foreach($blocks as $block) {
                $block = $mandango->create('Model\PHPOrchestraCMSBundle\Block')
                    ->setComponent($block['component'])  
                    ->setAttributes($block['attributes']);
                $template->addBlocks($block);
            }
        }
        
        return $template;
    }
        
}