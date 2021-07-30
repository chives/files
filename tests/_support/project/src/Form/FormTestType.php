<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\FSi\App\Form;

use FSi\Component\Files\Integration\Symfony\Form\WebFileType;
use FSi\Component\Files\Integration\Symfony\Validator\Constraint\UploadedImage;
use FSi\Component\Files\Integration\Symfony\Validator\Constraint\UploadedWebFile;
use Tests\FSi\App\Entity\FileEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class FormTestType extends AbstractType
{
    /**
     * @param FormBuilderInterface<FormBuilderInterface> $builder
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', WebFileType::class, [
            'label' => 'Standard file',
            'constraints' => [new UploadedWebFile()],
            'removable' => true,
            'required' => false
        ]);

        $builder->add('anotherFile', WebFileType::class, [
            'label' => 'Image file',
            'constraints' => [new NotBlank(), new UploadedImage()],
            'image' => true,
            'required' => false
        ]);

        $builder->add('privateFile', WebFileType::class, [
            'label' => 'Private file',
            'constraints' => [new NotBlank()],
            'image' => true,
            'required' => false,
            'resolve_url' => false
        ]);

        $builder->add('embeddedFile', EmbeddedFileType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', FileEntity::class);
        $resolver->setDefault('method', Request::METHOD_POST);
    }
}
