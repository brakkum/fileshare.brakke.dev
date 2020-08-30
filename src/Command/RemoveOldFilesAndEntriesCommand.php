<?php
namespace App\Command;

use App\Entity\SharedFile;
use App\Utilities\Constants;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveOldFilesAndEntriesCommand extends Command
{
    protected static $defaultName = "app:remove-files";
    private $entity_manager;
    private $path;
    private $logger;

    public function __construct(string $path, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->path = $path;
        $this->entity_manager = $em;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription("Removes old files and database records")
            ->setHelp("Finds files older than the allowed max age or that have reached their download limit and removes them");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->entity_manager;
        $repo = $em->getRepository(SharedFile::class);

        $old_files = $repo->getDownloadsOutOrOlderThanLimit();

        if ($old_files != null) {
            foreach ($old_files as $file) {
                $file_path = $this->path.Constants::UPLOAD_DIRECTORY.$file->getHashOfFileContents();
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                $em->remove($file);
            }
            $em->flush();
        }

        return Command::SUCCESS;
    }
}
