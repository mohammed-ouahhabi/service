<?php

namespace App\Form;

use App\Entity\Produits;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use App\Entity\Categorie;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom', TextType::class)
            ->add('Prix', NumberType::class)
            ->add('Taille', TextType::class)
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class, // Specify the class of the entity
                'choice_label' => 'nom',
                'placeholder' => 'Choose a category'])
            ->add('brochure', FileType::class, [
                'label' => 'Product Image(des fichiers image uniquement)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',

                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file',
                    ])
                ],
            ]);
    }

    /*         ->add('brochure', FileType::class, [
                 'label' => 'Brochure (PDF file)',  // Updated label
                 'mapped' => false,
                 'required' => false,
                 'constraints' => [
                     new Image([  // Changed from File to Image constraint
                         'maxSize' => '50M',
                         'mimeTypes' => [
                             'image/jpeg',
                             'image/png',
                             'image/gif',
                         ],
                         'mimeTypesMessage' => 'Please upload a valid image or PDF file',
                     ])
                 ],
             ]);
     }*/

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
