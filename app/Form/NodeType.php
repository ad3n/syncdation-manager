<?php

declare(strict_types=1);

namespace KejawenLab\Application\Form;

use KejawenLab\Application\Entity\Node;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class NodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('code', null, [
            'required' => true,
            'label' => 'sas.form.field.node.code',
        ]);
        $builder->add('name', null, [
            'required' => true,
            'label' => 'sas.form.field.node.name',
        ]);
        $builder->add('host', null, [
            'required' => true,
            'label' => 'sas.form.field.node.host',
        ]);
        $builder->add('prefix', null, [
            'required' => false,
            'label' => 'sas.form.field.node.prefix',
        ]);
        $builder->add('apiKey', null, [
            'required' => true,
            'label' => 'sas.form.field.node.apiKey',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Node::class,
            'translation_domain' => 'forms',
        ]);
    }
}
