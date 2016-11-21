<?php

namespace Mykbas\NestablePageBundle\Controller;

use Mykbas\NestablePageBundle\Model\PageMetaBase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * PageMeta controller.
 *
 * @Route("mykbas_pagemeta")
 */
class PageMetaController extends Controller
{
    private $entity;

    private $entity_meta;

    private $page_meta_form_type;

    private $pagemeta_view_index;

    private $pagemeta_view_new;

    private $pagemeta_view_edit;

    private $pagemeta_view_show;

    public function init()
    {
        $this->entity = $this->container->getParameter('mykbas_nestable_page.page_entity');
        $this->entity_meta = $this->container->getParameter('mykbas_nestable_page.pagemeta_entity');
        $this->page_meta_form_type = $this->container->getParameter('mykbas_nestable_page.pagemeta_form_type');
        $this->pagemeta_view_index = $this->container->getparameter('mykbas_nestable_page.pagemeta_view_index');
        $this->pagemeta_view_new = $this->container->getparameter('mykbas_nestable_page.pagemeta_view_new');
        $this->pagemeta_view_edit = $this->container->getparameter('mykbas_nestable_page.pagemeta_view_edit');
        $this->pagemeta_view_show = $this->container->getparameter('mykbas_nestable_page.pagemeta_view_show');
    }

    /**
     * Lists all pageMeta entities.
     *
     * @Route("/", name="mykbas_pagemeta_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pageMetas = $em->getRepository($this->entity_meta)->findAll();

        return $this->render($this->pagemeta_view_index, ['pageMetas' => $pageMetas]);
    }

    /**
     * Creates a new pageMeta entity.
     *
     * @Route("/new", name="mykbas_pagemeta_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $pageMeta = new $this->entity_meta();
        $form = $this->createForm($this->page_meta_form_type, $pageMeta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($em->getRepository($this->entity_meta)->findPageMetaByLocale($pageMeta->getPage(), $pageMeta->getLocale())) {
                $this->get('session')->getFlashBag()
                    ->add('error', $this->get('translator')
                        ->trans('one_locale_per_pagemeta_only', array(), 'MykbasNestablePageBundle')
                    );
            } else {
                $em->persist($pageMeta);
                $em->flush();

                return $this->redirectToRoute('mykbas_pagemeta_show', ['id' => $pageMeta->getId()]);
            }
        }

        return $this->render($this->pagemeta_view_new, [
            'pageMeta' => $pageMeta,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a pageMeta entity.
     *
     * @Route("/{id}", name="mykbas_pagemeta_show")
     * @Method("GET")
     */
    public function showAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $pageMeta = $em->getRepository($this->entity_meta)->find($request->get('id'));

        $deleteForm = $this->createDeleteForm($pageMeta);

        return $this->render($this->pagemeta_view_show, [
            'pageMeta' => $pageMeta,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing pageMeta entity.
     *
     * @Route("/{id}/edit", name="mykbas_pagemeta_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $pageMeta = $em->getRepository($this->entity_meta)->find($request->get('id'));
        $origId = $pageMeta->getPage()->getId();
        $origLocale = $pageMeta->getLocale();

        $deleteForm = $this->createDeleteForm($pageMeta);
        $editForm = $this->createForm($this->page_meta_form_type, $pageMeta);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $error = false;

            // if page and local is the same, no need to check locale count
            if ($origLocale == $pageMeta->getLocale() && $origId == $pageMeta->getPage()->getId()) {
                // all good
            } elseif ($em->getRepository($this->entity_meta)->findPageMetaByLocale($pageMeta->getPage(), $pageMeta->getLocale(), true)) {
                $this->get('session')->getFlashBag()
                    ->add('error', $this->get('translator')
                        ->trans('one_locale_per_pagemeta_only', array(), 'MykbasNestablePageBundle')
                    );
                $error = true;
            }

            // if everything is successful
            if (!$error) {
                $em->persist($pageMeta);
                $em->flush();

                return $this->redirectToRoute('mykbas_pagemeta_edit', ['id' => $pageMeta->getId()]);
            }
        }

        return $this->render($this->pagemeta_view_edit, [
            'pageMeta' => $pageMeta,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a pageMeta entity.
     *
     * @Route("/{id}", name="mykbas_pagemeta_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $pageMeta = $em->getRepository($this->entity_meta)->find($request->get('id'));
        $form = $this->createDeleteForm($pageMeta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pageMeta);
            $em->flush();
        }

        return $this->redirectToRoute('mykbas_pagemeta_index');
    }

    /**
     * Creates a form to delete a pageMeta entity.
     *
     * @param PageMetaBase $pageMeta The pageMeta entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PageMetaBase $pageMeta)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mykbas_pagemeta_delete', ['id' => $pageMeta->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
