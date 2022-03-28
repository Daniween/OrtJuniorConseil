<?php

namespace App\Form;

use App\Entity\Etude;
use App\Entity\Etudiant;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EtudiantType
 * @package App\Form
 */
class EtudiantType extends AbstractType
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
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => false
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('num', TextType::class, [
                'label' => 'Téléphone',
                'required' => false
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
            ])
            ->add('avatar', FileType::class, [
                'label' => "Photo de profil",
                'data_class' => null,
                'empty_data' => '',
                'required' => false
            ])
            ->add('document', FileType::class, [
                'label' => "CV",
                'data_class' => null,
                'empty_data' => '',
                'required' => false
            ])
            ->add('birthDate', DateType::class, [
                'label' => "Date de naissance",
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'required' => false,
            ])
            ->add('etude', EntityType::class, [
                'class' => Etude::class,
                'placeholder' => 'Choisissez un niveau d\'étude',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.status = :status')
                        ->setParameter('status', Etude::TYPE_PUBLIC);
                },
                'label' => 'Niveau d\'étude',
                'choice_label' => function ($etude) {
                    return $etude->getLibelle();
                }
            ]);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
