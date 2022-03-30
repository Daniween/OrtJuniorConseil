<?php

namespace App\Form;

use App\Entity\Admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AdminType
 * @package App\Form
 */
class AdminType extends AbstractType
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'              => PasswordType::class,
                'invalid_message'   => 'The password fields must match.',
                'options'           => [
                    'attr' => [
                        'class' => 'password-field'
                    ]
                ],
                'constraints' => [
                    new NotBlank([
                        'groups' => [
                            'create'
                        ],
                    ]),
                ],
                'first_options'     => [
                    'label' => 'Mot de passe'
                ],
                'second_options'    => [
                    'label' => 'Confirmez le mot de passe'
                ],
                'mapped' => false,
                'required' => false
            ]);
        if ($this->security->isGranted('ROLE_ADMIN')) {

            $builder->add('roles', ChoiceType::class, [
                'label'     => 'Rôle',
                'choices'   => [
                    'Admin' => Admin::ROLE_ADMIN
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'label_attr' => ['class' => 'text-xs']

            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'Actif',
                'label_attr' => ['class' => 'checkbox-switch'],
                'required' => false,
            ]);
        }

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Admin::class,
        ]);
    }
}
