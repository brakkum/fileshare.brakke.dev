<?php
namespace App\Form\Type;

use App\Entity\SharedFile;
use App\Utilities\Constants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SharedFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $max_file_size = Constants::MAX_FILE_SIZE;

        $builder
            ->add("file", FileType::class, [
                "label" => "File",
                "mapped" => false,
                "required" => true,
                "constraints" => [
                    new File([
                        "maxSize" => $max_file_size,
                        "maxSizeMessage" => "File size must be smaller than $max_file_size"
                    ])
                ],
                "attr" => [
                    "class" => "input"
                ]
            ])
            ->add("allowed_downloads", IntegerType::class, [
                "label" => "Number of allowed downloads",
                "mapped" => true,
                "required" => false,
                "data" => "1",
                "attr" => [
                    "class" => "input",
                    "min" => 1
                ]
            ])
            ->add("private_key", TextType::class, [
                "label" => "Secret phrase needed to download",
                "mapped" => true,
                "required" => false,
                "attr" => [
                    "class" => "input"
                ]
            ])
            ->add("submit", SubmitType::class, [
                'row_attr' => [
                    'class' => 'button-row'
                ],
                "attr" => [
                    "class" => "button is-success"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SharedFile::class,
        ]);
    }
}