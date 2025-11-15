<?php

namespace App\Form;

use App\Entity\OrderEntity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer', EntityType::class, ['class' => \App\Entity\Customer::class, 'choice_label' => 'name'])
            ->add('dishes', EntityType::class, ['class' => \App\Entity\Dish::class, 'choice_label' => 'name', 'multiple' => true, 'expanded' => true])
            ->add('documentFile', VichFileType::class, [
                'label' => 'Документ',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Удалить',
                'download_uri' => true,
                'constraints' => [new File([
                    'maxSize' => '10m',
                    'mimeTypes' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'image/jpeg', 'image/png'],
                ])],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => OrderEntity::class]);
    }
}
