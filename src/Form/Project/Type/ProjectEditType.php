<?php

declare(strict_types=1);

namespace App\Form\Project\Type;

use App\Entity\Project;
use App\Form\DataTransformer\CategoryToCollectionTransfomer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ProjectEditType extends AbstractType
{
    /** @required */
    public RouterInterface $router;
    /** @required */
    public CategoryToCollectionTransfomer $categoryToCollectionTransfomer;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'rows' => 5,
                ],
            ])->add('categories', TextType::class, [
                'attr' => [
                    'autocomplete_url' => $this->router->generate('category_autocomplete'),
                    'label' => 'Categories',
                ],
                'label' => false,
                'block_prefix' => 'autocomplete',
            ]);

        $builder
            ->get('categories')
            ->addModelTransformer($this->categoryToCollectionTransfomer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
