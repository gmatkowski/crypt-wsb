<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use MiladRahimi\PhpCrypt\Exceptions\DecryptionException;
use MiladRahimi\PhpCrypt\Exceptions\EncryptionException;
use MiladRahimi\PhpCrypt\Exceptions\InvalidKeyException;
use MiladRahimi\PhpCrypt\PrivateRsa;
use MiladRahimi\PhpCrypt\PublicRsa;


/**
 *
 */
class Crypt extends Command
{
    /**
     *
     */
    public const FILE_ACTION = 'file';
    /**
     *
     */
    public const MESSAGE_ACTION = 'message';
    /**
     *
     */
    public const ENCRYPT = 'encrypt';
    /**
     *
     */
    public const DECRYPT = 'decrypt';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:crypt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crypt & Decrypt files or messages.';

    /**
     * @var PrivateRsa
     */
    protected PrivateRsa $privateRsa;
    /**
     * @var PublicRsa
     */
    protected PublicRsa $publicRsa;

    /**
     *
     */
    public function handle(): void
    {
        $this->prepare();

        $type = $this->choice('Choose your action type', [
            self::FILE_ACTION,
            self::MESSAGE_ACTION
        ]);

        $action = $this->choice('Choose whether to encrypt or decrypt message', [
            self::ENCRYPT,
            self::DECRYPT
        ]);

        $methodName = sprintf('%sAction', strtolower($type));
        $this->{$methodName}($action);
    }

    /**
     *
     */
    protected function fileAction(string $action): void
    {
        $filepath = $this->ask('Please provide filepath');
        if (!File::exists($filepath)) {
            $this->error('File not found!');
            return;
        }

        $this->info(
            $this->{$action}(File::get($filepath))
        );
    }

    /**
     *
     */
    protected function messageAction(string $action): void
    {
        $message = $this->ask('Please provide message:');
        $this->info(
            $this->{$action}($message)
        );
    }

    /**
     * @param string $text
     * @return string
     */
    private function encrypt(string $text): string
    {
        try {
            return $this->publicRsa->encrypt($text);
        } catch (EncryptionException $exception) {
            $this->error($exception->getMessage());
            exit();
        }
    }

    /**
     * @param string $text
     * @return string
     */
    private function decrypt(string $text): string
    {
        try {
            return $this->privateRsa->decrypt($text);
        } catch (DecryptionException $exception) {
            $this->error($exception->getMessage());
            exit();
        }
    }

    /**
     *
     */
    protected function prepare(): void
    {
        try {
            $this->privateRsa = new PrivateRsa(config('crypt.keys.private'));
            $this->publicRsa = new PublicRsa(config('crypt.keys.public'));
        } catch (InvalidKeyException $exception) {
            $this->error($exception->getMessage());
            exit();
        }
    }
}
