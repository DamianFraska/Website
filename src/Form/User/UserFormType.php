<?php

declare(strict_types=1);

namespace App\Form\User;

use App\Entity\Permissions\UserPermissions;
use App\Form\Permissions\PermissionsType;
use App\Form\User\Dto\UserFormDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('steamId', TextType::class, [
                'label' => 'Steam ID',
            ])
            ->add('permissions', PermissionsType::class, [
                'label' => 'User permissions',
                'data_class' => UserPermissions::class,
                'target' => $options['target'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserFormDto::class,
            'required' => false,
            'target' => null,
        ]);
    }
}
