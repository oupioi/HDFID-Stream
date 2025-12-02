<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(message: 'Le prénom est obligatoire.'),
                    new Length(min: 2, max: 100, minMessage: 'Prénom trop court.', maxMessage: 'Prénom trop long.'),
                ],
                'attr' => [
                    'autocomplete' => 'given-name',
                    'placeholder' => 'Antoine',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(message: 'Le nom est obligatoire.'),
                    new Length(min: 2, max: 100, minMessage: 'Nom trop court.', maxMessage: 'Nom trop long.'),
                ],
                'attr' => [
                    'autocomplete' => 'family-name',
                    'placeholder' => 'Dupont',
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Pseudo (optionnel)',
                'required' => false,
                'constraints' => [
                    new Length(max: 50, maxMessage: '50 caractères maximum.'),
                ],
                'attr' => [
                    'autocomplete' => 'nickname',
                    'placeholder' => 'ADupont',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'constraints' => [
                    new NotBlank(message: 'L\'email est requis.'),
                    new Email(message: 'Format d\'email invalide.'),
                ],
                'attr' => [
                    'autocomplete' => 'email',
                    'placeholder' => 'antoine.dupont@ffr.com',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => '••••••••',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmez le mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => '••••••••',
                    ],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'constraints' => [
                    new NotBlank(message: 'Veuillez définir un mot de passe.'),
                    new Length(min: 8, max: 4096, minMessage: '8 caractères minimum.'),
                    new Regex(
                        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
                        message: 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.'
                    ),
                    new NotCompromisedPassword(message: 'Veuillez en choisir un autre mot de passe.'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
