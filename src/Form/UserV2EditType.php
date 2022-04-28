<?php

namespace App\Form;

use App\Entity\UserV2;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserV2EditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login')
            ->add('password',PasswordType::class)
            ->add('newPassword',PasswordType::class, ['required' => false,'label' => 'Nouveau mot de passe(optionnel)','empty_data'  => null])
            ->add('name')
            ->add('surname')
            ->add('date_birth', DateType::class, array(
                'label' => 'Date de naissance',
                'format' => 'dd MM yyyy',
                'years' => range(date('1900'), date('Y')),
                'required' => true))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => UserV2::class,
        ]);
    }
}
