<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
        {
                $builder
                        ->add('email')
                        // suppression du role qui sera défini par défaut
                        ->add('password', RepeatedType::class, [
                                'type' => PasswordType::class,
                                'invalid_message' => 'The password fields must match.',
                                'options' => ['attr' => ['class' => 'password-field']],
                                'required' => true,
                                'first_options'  => ['label' => 'Password'],
                                'second_options' => ['label' => 'Repeat Password'],
                                'options' => ['attr' => ['class' => 'password-field']],
                                ]);
                ;
        }

        public function configureOptions(OptionsResolver $resolver)
        {
                $resolver->setDefaults([
                'data_class' => Utilisateur::class,
                ]);
        }
}
