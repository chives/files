<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Tests\App\Form;

use FSi\Component\Files\Integration\Symfony\Form\WebFileType;
use FSi\Component\Files\Integration\Symfony\Validator\Constraint\UploadedImage;
use FSi\Tests\App\Entity\EmbeddedFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class EmbeddedFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', WebFileType::class, [
            'label' => 'Removable embedded image',
            'constraints' => [new NotBlank(), new UploadedImage()],
            'image' => true,
            'removable' => true,
            'required' => false
        ]);

        $builder->add('embeddedFile', TwiceEmbeddedFileType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', EmbeddedFile::class);
        $resolver->setDefault('label', false);
    }
}