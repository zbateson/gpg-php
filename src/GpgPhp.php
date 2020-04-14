<?php
/**
 * This file is part of the zbateson\gpg-interface project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\GpgPhp;

use Exception;
use GuzzleHttp\Psr7;
use Psr\Http\Message\StreamInterface;
use ZBateson\CryptInterface\AbstractCrypt;

/**
 * Implementation of ICrypt using pecl/gnupg
 *
 * @author Zaahid Bateson
 */
class GpgPhp extends AbstractCrypt
{
    /**
     * @var resource gnupg init resource handle
     */
    protected $gpg;

    /**
     * Default constructor takes an optional Crypt_GPG object.  If not passed, a
     * new instance of Crypt_GPG is created.
     */
    public function __construct($gpg = null)
    {
        if ($gpg === null) {
            $gpg = \gnupg_int();
        }
        $this->gpg = $gpg;
    }

    /**
     * Returns a StreamInterface of the encrypted data contained in the passed
     * stream, or false on failure.
     *
     * @return StreamInterface|boolean
     */
    protected function encryptStream(StreamInterface $in)
    {
        $ret = \gnupg_encrypt($this->gpg, $in->getContents());
        if ($ret === false) {
            return $ret;
        }
        return Psr7\stream_for($ret);
    }

    /**
     * Returns a StreamInterface of the decrypted data contained in the passed
     * stream, or false on failure.
     *
     * @return StreamInterface|boolean
     */
    protected function decryptStream(StreamInterface $in)
    {
        $ret = \gnupg_decrypt($this->gpg, $in->getContents());
        if ($ret === false) {
            return $ret;
        }
        return Psr7\stream_for($ret);
    }

    /**
     * Returns the signature of the passed stream, or false on failure.
     *
     * @return string|boolean
     */
    protected function signStream(StreamInterface $in)
    {
        \gnupg_setsignmode($this->gpg, GNUPG_SIG_MODE_DETACH);
        return \gnupg_sign($this->gpg, $in->getContents());
    }

    /**
     * Returns either true if the passed data has been signed with the passed
     * $signature and has been verified, or false otherwise.
     *
     * @return boolean
     */
    protected function verifyStream(StreamInterface $in, $signature)
    {
        return (\gnupg_verify($this->gpg, $in->getContents(), $signature) !== false);
    }

    /**
     * Returns true for:
     *  - application/(x-)?pgp-encrypted (if version equals the string
     *    'Version: 1')
     *  - application/(x-)?pgp-signature
     */
    public function isSupported($mimeType, $version = null)
    {
        if (preg_match('/^application\/(x-)?pgp-encrypted$/', $mimeType)) {
            return (strcasecmp('Version: 1', trim($version)) === 0);
        }
        return (bool) preg_match('/^application\/(x-)?pgp-signature$/', $mimeType);
    }
}
