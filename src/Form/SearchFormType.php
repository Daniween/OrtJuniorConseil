<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Competence;
use App\Entity\Personnalite;
use App\Entity\Etude;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('q', TextType::class, [
                'label' => "Recherche par : nom ou prénom",
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher par nom'
                ]
            ])

            ->add('competence', EntityType::class, [
                'label' => "Compétences",
                'required' => false,
                'class' => Competence::class,
                'expanded' => false,
                'multiple' => true,
                'choice_label' => function ($competence) {
                    return $competence->getLibelle();
                }
            ])
            ->add('personnalite', EntityType::class, [
                'label' => "Soft Skill",
                'required' => false,
                'class' => Personnalite::class,
                'expanded' => false,
                'multiple' => true,
                'choice_label' => function ($personnalite) {
                    return $personnalite->getLibelle();
                }
            ])
            ->add('etude', EntityType::class, [
                'label' => "Niveau d'étude",
                'required' => false,
                'class' => Etude::class,
                'expanded' => false,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.status != :status')
                        ->setParameter('status', Etude::TYPE_TRASH);
                },
                'choice_label' => function ($etude) {
                    return $etude->getLibelle();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
