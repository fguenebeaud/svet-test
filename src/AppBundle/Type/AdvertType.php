<?php

namespace AppBundle\Type;

use AppBundle\Entity\Advert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AdvertType
 * @package AppBundle\Type
 */
class AdvertType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                'text',
                array(
                    'label'    => 'Titre *:',
                    'required' => true,
                )
            )
            ->add(
                'place',
                'text',
                array(
                    'label'    => 'Lieu *:',
                    'required' => true,
                )
            )
            ->add(
                'price',
                'integer',
                array(
                    'label'    => 'Prix *:',
                    'required' => false,
                )
            )
            ->add(
                'link',
                'text',
                array(
                    'label' => 'URL *:',
                    'required' => true,
                )
            );
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Advert',
            )
        );
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'advert';
    }
}
