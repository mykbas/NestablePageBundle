<?php

namespace Mykbas\NestablePageBundle\PageTestBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Mykbas\NestablePageBundle\Form\PageMetaType as BasePageMetaType;

class PageMetaType extends BasePageMetaType
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
        $resolver->setDefaults(['data_class' => 'Mykbas\NestablePageBundle\PageTestBundle\Entity\PageMeta']);
    }
}
