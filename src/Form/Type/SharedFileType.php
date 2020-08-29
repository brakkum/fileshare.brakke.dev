<?php
namespace App\Form\Type;

use App\Entity\SharedFile;
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
        $max_file_size = MAX_FILE_SIZE;

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
                ]
            ])
            ->add("allowed_downloads", IntegerType::class, [
                "label" => "Number of allowed downloads",
                "mapped" => true,
                "required" => false,
                "data" => "1"
            ])
            ->add("private_key", TextType::class, [
                "label" => "Secret phrase needed to download",
                "mapped" => true,
                "required" => false,
                "data" => ""
            ])
            ->add("submit", SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SharedFile::class,
        ]);
    }
}