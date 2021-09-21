<?php

declare(strict_types=1);

namespace KejawenLab\Application\Form;

use KejawenLab\ApiSkeleton\Entity\EntityInterface;
use KejawenLab\Application\Entity\Endpoint;
use KejawenLab\Application\Node\Model\EndpointInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class EndpointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('node', null, [
            'required' => false,
            'label' => 'sas.form.field.endpoint.node',
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
        $builder->add('path', null, [
            'required' => true,
            'label' => 'sas.form.field.endpoint.path',
        ]);
        $builder->add('sql', null, [
            'required' => true,
            'label' => 'sas.form.field.endpoint.sql',
        ]);
        $builder->add('defaults', null, [
            'required' => false,
            'label' => 'sas.form.field.endpoint.defaults',
        ]);
        $builder->get('defaults')
            ->addModelTransformer(new CallbackTransformer(
            function ($defaults) {
                if (null === $defaults) {
                    $defaults = [];
                }

                return json_encode($defaults);
            },
            function ($defaults) {
                return json_decode($defaults, true);
            }
        ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $endpoint = $event->getData();
            $form = $event->getForm();

            if ($endpoint instanceof EndpointInterface) {
                if (null !== $endpoint->getPath()) {
                    $form->remove('path');
                    $form->add('path', null, [
                        'required' => true,
                        'label' => 'sas.form.field.endpoint.path',
                        'attr' => [
                            'readonly' => true,
                        ]
                    ]);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Endpoint::class,
            'translation_domain' => 'forms',
        ]);
    }
}
