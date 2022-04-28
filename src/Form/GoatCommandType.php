<?php

namespace App\Form;

use App\Entity\GoatCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GoatCommandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('race', ChoiceType::class, ['label' => 'Race ','choices'=>['race'=>$options['race']],'choice_label'=>function($val){return $val;}])

            ->add('quantite', ChoiceType::class, ['label' => 'Quantité ','choices'=>['quantite'=>$options['quantite']]])
            //l'option "choices" permet de préremplir les différents choix du formulaire selon l'attribut "options" rempli lors de l'appel de création du formulaire dans GoatController:achatAction

            ->add('submit',SubmitType::class,['label'=>'Commander'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GoatCommand::class,
            'quantite'=>[],
            'race'=>[]
            ]);
    }
}
