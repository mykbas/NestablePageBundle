<?php

namespace Mykbas\NestablePageBundle\PageTestBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Mykbas\NestablePageBundle\Form\PageType as BasePageType;

class PageType extends BasePageType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Mykbas\NestablePageBundle\PageTestBundle\Entity\Page']);
    }
}
