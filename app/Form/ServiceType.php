<?php

declare(strict_types=1);

namespace KejawenLab\Application\Form;

use KejawenLab\ApiSkeleton\Entity\EntityInterface;
use KejawenLab\Application\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('node', null, [
            'required' => false,
            'label' => 'sas.form.field.service.node',
            'choice_label' => function ($node) {
                if ($node instanceof EntityInterface) {
                    return $node->getNullOrString();
                }

                return (string) $node;
            },
            'attr' => [
                'class' => 'select2',
            ],
            'placeholder' => 'sas.form.field.empty_select',
        ]);
        $builder->add('name', null, [
            'required' => true,
            'label' => 'sas.form.field.service.name',
        ]);
        $builder->add('type', null, [
            'required' => true,
            'label' => 'sas.form.field.service.type',
        ]);
        $builder->add('status', null, [
            'required' => true,
            'label' => 'sas.form.field.service.status',
        ]);
        $builder->add('processed', null, [
            'required' => true,
            'label' => 'sas.form.field.service.processed',
        ]);
        $builder->add('successed', null, [
            'required' => true,
            'label' => 'sas.form.field.service.successed',
        ]);
        $builder->add('failed', null, [
            'required' => true,
            'label' => 'sas.form.field.service.failed',
        ]);
        $builder->add('clients', null, [
            'required' => true,
            'label' => 'sas.form.field.service.clients',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
            'translation_domain' => 'forms',
        ]);
    }
}
