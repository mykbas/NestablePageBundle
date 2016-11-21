<?php

namespace Mykbas\NestablePageBundle\Controller;

use Mykbas\NestablePageBundle\Model\PageBase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Mykbas\NestablePageBundle\Form\PageType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Page controller.
 *
 * @Route("mykbas_page")
 */
class PageController extends Controller
{
    private $entity;

    private $entity_meta;

    private $page_form_type;

    private $page_view_list;

    private $page_view_new;

    private $page_view_edit;

    private $page_view_show;

    public function init()
    {
        $this->entity = $this->container->getParameter('mykbas_nestable_page.page_entity');
        $this->entity_meta = $this->container->getParameter('mykbas_nestable_page.pagemeta_entity');
        $this->page_form_type = $this->container->getParameter('mykbas_nestable_page.page_form_type');
        $this->page_view_list = $this->container->getparameter('mykbas_nestable_page.page_view_list');
        $this->page_view_new = $this->container->getparameter('mykbas_nestable_page.page_view_new');
        $this->page_view_edit = $this->container->getparameter('mykbas_nestable_page.page_view_edit');
        $this->page_view_show = $this->container->getparameter('mykbas_nestable_page.page_view_show');
    }

    /**
     * Lists all page entities.
     *
     * @Route("/", name="mykbas_page_index")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('mykbas_page_list'));
    }

    /**
     * Lists all nested page
     *
     * @Route("/list", name="mykbas_page_list")
     * @Method("GET")
     *
     * @return array
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $rootMenuItems = $em->getRepository($this->entity)->findParent();

        return $this->render($this->page_view_list, ['tree' => $rootMenuItems]);
    }

    /**
     * Reorder pages
     *
     * @Route("/reorder", name="mykbas_page_reorder")
     * @Method("POST")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reorderAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // id of affected element
        $id = $request->get('id');

        // if invalid token, fail silently
        if (!$this->isCsrfTokenValid('mykbas_page_reorder', $request->get('csrf'))) {
            // fail silently
            return;
        }

        // parent Id
        $parentId = ($request->get('parentId') == '') ? null : $request->get('parentId');
        // new sequence of this element. 0 means first element.
        $position = $request->get('position');

        $result = $em->getRepository($this->entity)->reorderElement($id, $parentId, $position);

        return new JsonResponse([
            'message' => $this->get('translator')->trans($result[0], [], 'MykbasNestablePageBundle'),
            'success' => $result[1],
        ]);
    }

    /**
     * Creates a new page entity.
     *
     * @Route("/new", name="mykbas_page_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request)
    {
        $page = new $this->entity();
        $form = $this->createForm($this->page_form_type, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            return $this->redirectToRoute('mykbas_page_show', ['id' => $page->getId()]);
        }

        return $this->render($this->page_view_new, [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a page entity.
     *
     * @Route("/{id}", name="mykbas_page_show")
     * @Method("GET")
     *
     * @param Request $request
     * @return array
     */
    public function showAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository($this->entity)->find($request->get('id'));

        $pageMeta = $em->getRepository($this->entity_meta)->findPageMetaByLocale($page, $request->getLocale());

        $deleteForm = $this->createDeleteForm($page);

        return $this->render($this->page_view_show, [
            'page' => $page,
            'pageMeta' => $pageMeta,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing page entity.
     *
     * @Route("/{id}/edit", name="mykbas_page_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository($this->entity)->find($request->get('id'));
        $deleteForm = $this->createDeleteForm($page);
        $editForm = $this->createForm($this->page_form_type, $page);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            return $this->redirectToRoute('mykbas_page_edit', ['id' => $page->getId()]);
        }

        return $this->render($this->page_view_edit, [
            'page' => $page,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a page entity.
     *
     * @Route("/{id}", name="mykbas_page_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository($this->entity)->find($request->get('id'));
        $form = $this->createDeleteForm($page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($page);
            $em->flush();
        }

        return $this->redirectToRoute('mykbas_page_index');
    }

    /**
     * Creates a form to delete a page entity.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PageBase $page)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mykbas_page_delete', ['id' => $page->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
