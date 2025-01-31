<?php

declare(strict_types=1);

namespace App\Cache\CacheWarmer;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;

class BackgroundImageCacheWarmer extends CacheWarmer
{
    public function __construct(
        private string $backgroundImagesDirectory
    ) {
    }

    public static function getCacheFileName(): string
    {
        return 'background_images.php';
    }

    public function warmUp(string $cacheDir): void
    {
        $fileTemplate = '<?php return %s;';
        $this->writeCacheFile(
            $cacheDir.'/'.static::getCacheFileName(),
            sprintf($fileTemplate, var_export($this->findImagesToCache(), true))
        );
    }

    public function isOptional(): bool
    {
        return false;
    }

    protected function findImagesToCache(): array
    {
        $imagesIterator = (Finder::create())->in($this->backgroundImagesDirectory)->files()->getIterator();

        $images = array_map(static fn (\SplFileInfo $imageFile) => $imageFile->getFilename(), iterator_to_array($imagesIterator));

        return array_values($images);
    }
}
