<?php

declare(strict_types=1);

namespace KejawenLab\Application\Form;

use KejawenLab\ApiSkeleton\Entity\EntityInterface;
use KejawenLab\Application\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class DatabaseServiceType extends AbstractType
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
            'required' => false,
            'label' => 'sas.form.field.service.name',
        ]);
        $builder->add('driver', null, [
            'required' => false,
            'label' => 'sas.form.field.service.driver',
        ]);
        $builder->add('dbHost', null, [
            'required' => false,
            'label' => 'sas.form.field.service.host',
        ]);
        $builder->add('dbPort', null, [
            'required' => false,
            'label' => 'sas.form.field.service.port',
        ]);
        $builder->add('dbUser', null, [
            'required' => false,
            'label' => 'sas.form.field.service.username',
        ]);
        $builder->add('dbPassword', null, [
            'required' => false,
            'label' => 'sas.form.field.service.password',
        ]);
        $builder->add('dbName', null, [
            'required' => false,
            'label' => 'sas.form.field.service.dbname',
        ]);
        $builder->add('dbTable', null, [
            'required' => false,
            'label' => 'sas.form.field.service.table',
        ]);
        $builder->add('dbColumns', null, [
            'required' => false,
            'label' => 'sas.form.field.service.column',
            'help' => 'sas.form.field.service.multiple_help',
        ]);
        $builder->add('type', HiddenType::class, [
            'required' => false,
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
