<?php

declare(strict_types=1);

namespace Pollen\Kernel\Filesystem;

use Pollen\Filesystem\FilesystemInterface;
use Pollen\Filesystem\LocalFilesystemInterface;
use Pollen\Filesystem\StorageManagerInterface;

interface PathInterface extends StorageManagerInterface
{
    /**
     * {@inheritDoc}
     *
     * @return LocalFilesystemInterface|null
     */
    public function disk(?string $name = null): ?FilesystemInterface;

    /**
     * Récupération de l'instance du gestionnaire de dossier racine.
     *
     * @return LocalFilesystemInterface
     */
    public function diskBase(): LocalFilesystemInterface;

    /**
     * Récupération de l'instance du gestionnaire du dossier de stockage des fichiers de cache.
     *
     * @return LocalFilesystemInterface
     */
    public function diskCache(): LocalFilesystemInterface;

    /**
     * Récupération de l'instance du gestionnaire du dossier de stockage des fichiers de configuration.
     *
     * @return LocalFilesystemInterface
     */
    public function diskConfig(): LocalFilesystemInterface;

    /**
     * Récupération de l'instance du gestionnaire du dossier de stockage des fichiers de journalisation.
     *
     * @return LocalFilesystemInterface
     */
    public function diskLog(): LocalFilesystemInterface;

    /**
     * Récupération du chemin de gestionnaire de fichiers par rapport à la racine.
     *
     * @param LocalFilesystemInterface $disk
     * @param string $path Chemin absolue vers un dossier ou un fichier du gestionnaire.
     * @param boolean $absolute Activation de la récupération du chemin en absolu.
     *
     * @return string
     */
    public function diskPathFromBase(LocalFilesystemInterface $disk, string $path = '', bool $absolute = true): ?string;

    /**
     * Récupération de l'instance du gestionnaire du dossier publique.
     *
     * @return LocalFilesystemInterface
     */
    public function diskPublic(): LocalFilesystemInterface;

    /**
     * Récupération de l'instance du gestionnaire du dossier de stockage.
     *
     * @return LocalFilesystemInterface
     */
    public function diskStorage(): LocalFilesystemInterface;

    /**
     * Récupération de l'instance du gestionnaire du dossier du theme.
     *
     * @return LocalFilesystemInterface
     */
    public function diskTheme(): LocalFilesystemInterface;

    /**
     * Récupération du chemin vers un dossier ou un fichier du répertoire de base (racine).
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du repertoire.
     * @param bool $absolute Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getBasePath(string $path = '', bool $absolute = true): string;

    /**
     * Récupération du chemin vers un dossier ou un fichier du répertoire de cache.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $absolute Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getCachePath(string $path = '', bool $absolute = true): string;

    /**
     * Récupération du chemin vers un dossier ou un fichier du répertoire de configuration.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $absolute Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getConfigPath(string $path = '', bool $absolute = true): string;

    /**
     * Récupération du chemin vers un dossier ou un fichier du répertoire de journalisation.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $absolute Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getLogPath(string $path = '', bool $absolute = true): string;

    /**
     * Récupération du chemin vers un dossier ou un fichier du répertoire publique.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $absolute Activation de la sortie du chemin en relatif.
     *
     * @return string
     */
    public function getPublicPath(string $path = '', bool $absolute = true): string;

    /**
     * Récupération du chemin vers un dossier ou un fichier du répertoire de stockage.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $absolute Activation de la sortie du chemin en relatif.
     *
     * @return string
     */
    public function getStoragePath(string $path = '', bool $absolute = true): string;

    /**
     * Récupération du chemin vers un dossier ou un fichier du répertoire du thème.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $absolute Activation de la sortie du chemin en relatif.
     *
     * @return string
     */
    public function getThemePath(string $path = '', bool $absolute = true): string;

    /**
     * Vérification du type d'arborescence du projet.
     * @return boolean
     * @internal Vraie si les répertoires de Wordpress sont à la racine du projet.
     *
     */
    public function isWp(): bool;

    /**
     * Montage d'un disque local.
     *
     * @param string $name Nom de qualification.
     * @param string $root Chemin absolu vers la racine du disque.
     * @param array $config Configuration.
     *
     * @return LocalFilesystemInterface
     */
    public function mount(string $name, string $root, array $config = []): LocalFilesystemInterface;

    /**
     * Normalisation d'un chemin.
     *
     * @param string $path
     *
     * @return string
     */
    public function normalize(string $path): string;

    /**
     * Récupération du chemin par rapport à la racine.
     *
     * @param string $pathname Chemin absolue vers un dossier ou un fichier.
     *
     * @return string
     */
    public function relPathFromBase(string $pathname): ?string;
}