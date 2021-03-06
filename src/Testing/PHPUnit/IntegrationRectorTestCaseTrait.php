<?php declare(strict_types=1);

namespace Rector\Testing\PHPUnit;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use function Safe\sprintf;
use function Safe\substr;

/**
 * @inspiration https://github.com/nikic/TypeUtil/blob/37896d3643e9228332d4ab741048d9569e0b5fc3/test/IntegrationTest.php#L43-L59
 */
trait IntegrationRectorTestCaseTrait
{
    /**
     * @var string
     */
    private $splitLine = '#-----\n#';

    /**
     * @param string[]|string[][] $files
     */
    protected function doTestFiles(array $files): void
    {
        // 1. original to changed content
        foreach ($files as $file) {
            // 1. original to changed content
            if (is_array($file)) {
                $this->doTestFileMatchesExpectedContent($file[0], $file[1]);
            // 2. integration single file
            } else {
                $smartFileInfo = new SmartFileInfo($file);
                [$originalContent, $changedContent] = $this->splitContentToOriginalFileAndExpectedFile($smartFileInfo);
                $this->doTestFileMatchesExpectedContent($originalContent, $changedContent);
            }
        }
    }

    /**
     * @return string[]
     */
    protected function splitContentToOriginalFileAndExpectedFile(SmartFileInfo $smartFileInfo): array
    {
        if (Strings::match($smartFileInfo->getContents(), $this->splitLine)) {
            // original → expected
            [$originalContent, $expectedContent] = Strings::split($smartFileInfo->getContents(), $this->splitLine);
        } else {
            // no changes
            $originalContent = $smartFileInfo->getContents();
            $expectedContent = $originalContent;
        }

        $originalFile = $this->createTemporaryPathWithPrefix($smartFileInfo, 'original');
        $expectedFile = $this->createTemporaryPathWithPrefix($smartFileInfo, 'expected');

        FileSystem::write($originalFile, $originalContent);
        FileSystem::write($expectedFile, $expectedContent);

        // file needs to be autoload PHPStan analyze
        if ($this->autoloadTestFixture) {
            require_once $originalFile;
        }

        return [$originalFile, $expectedFile];
    }

    private function createTemporaryPathWithPrefix(SmartFileInfo $smartFileInfo, string $prefix): string
    {
        $hash = substr(md5($smartFileInfo->getPathname()), 0, 5);

        return sprintf(
            sys_get_temp_dir() . '/rector_temp_tests/%s_%s_%s',
            $prefix,
            $hash,
            $smartFileInfo->getBasename('.inc')
        );
    }
}
