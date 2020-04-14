<?php
namespace ZBateson\GpgPhp;

use PHPUnit\Framework\TestCase;

/**
 * Description of GpgPearTest
 *
 * @group GpgPear
 * @author Zaahid Bateson
 */
class GpgPearTest extends TestCase
{
    private $gpg;

    protected function setUp()
    {
        $homedir = dirname(__DIR__) . '/_data/keyring';
        putenv('GNUPGHOME=' . $homedir);

        $this->gpg = \gnupg_init();
        \gnupg_import($this->gpg, file_get_contents(dirname(__DIR__) . '/_data/private.gpg'));
        \gnupg_import($this->gpg, file_get_contents(dirname(__DIR__) . '/_data/public.gpg'));
    }

    public function testEncryptDecrypt()
    {
        $data = 'Queremos probar este funcion';
        $gpgPear = new GpgPhp($this->gpg);
        \gnupg_addencryptkey($this->gpg, 'zbateson@users.github.com');
        $stream = $gpgPear->encrypt($data);
        $this->assertNotFalse($stream);
        $dec = $gpgPear->decrypt($stream);
        $this->assertNotFalse($dec);
        $this->assertEquals($data, $dec->getContents());
    }

    public function testEncryptDecryptLargeFile()
    {
        $data = 'Queremos probar este funcion';
        while (strlen($data) < 10241) {
            $data .= $data;
        }
        $gpgPear = new GpgPhp($this->gpg);
        \gnupg_addencryptkey($this->gpg, 'zbateson@users.github.com');
        $stream = $gpgPear->encrypt($data);
        $this->assertNotFalse($stream);
        $dec = $gpgPear->decrypt($stream);
        $this->assertNotFalse($dec);
        $this->assertEquals($data, $dec->getContents());
    }

    public function testDecryptFail()
    {
        $gpgPear = new GpgPhp($this->gpg);
        $this->assertFalse($gpgPear->decrypt('blah-blah-blah'));
    }

    public function testSignVerify()
    {
        $data = 'Queremos probar este funcion';
        $gpgPear = new GpgPhp($this->gpg);
        \gnupg_addsignkey($this->gpg, 'zbateson@users.github.com');
        $signature = $gpgPear->sign($data);
        $this->assertTrue($gpgPear->verify($data, $signature));
    }

    public function testSignVerifyLargeFile()
    {
        $data = 'Queremos probar este funcion';
        while (strlen($data) < 10241) {
            $data .= $data;
        }
        $gpgPear = new GpgPhp($this->gpg);
        \gnupg_addsignkey($this->gpg, 'zbateson@users.github.com');
        $signature = $gpgPear->sign($data);
        $this->assertTrue($gpgPear->verify($data, $signature));
    }

    public function testVerifyInvalid()
    {
        $gpgPear = new GpgPhp($this->gpg);
        $this->assertFalse($gpgPear->verify('Test', 'blah-blah-blah'));
    }

    public function testPgpEncryptedSupported()
    {
        $gpgPear = new GpgPhp($this->gpg);
        $this->assertTrue($gpgPear->isSupported('application/pgp-encrypted', 'Version: 1'));
    }

    public function testPgpEncryptedWrongVersionUnsupported()
    {
        $gpgPear = new GpgPhp($this->gpg);
        $this->assertFalse($gpgPear->isSupported('application/pgp-encrypted', 'Version: 2'));
    }

    public function testXPgpEncryptedSupported()
    {
        $gpgPear = new GpgPhp($this->gpg);
        $this->assertTrue($gpgPear->isSupported('application/x-pgp-encrypted', 'Version: 1'));
    }

    public function testPgpEncryptedNullVersionUnsupported()
    {
        $gpgPear = new GpgPhp($this->gpg);
        $this->assertFalse($gpgPear->isSupported('application/pgp-encrypted'));
    }

    public function testPgpSignatureSupported()
    {
        $gpgPear = new GpgPhp($this->gpg);
        $this->assertTrue($gpgPear->isSupported('application/pgp-signature'));
    }

    public function testXPgpSignatureSupported()
    {
        $gpgPear = new GpgPhp($this->gpg);
        $this->assertTrue($gpgPear->isSupported('application/x-pgp-signature'));
    }

    public function testUnknownMimeTypeUnsupported()
    {
        $gpgPear = new GpgPhp($this->gpg);
        $this->assertFalse($gpgPear->isSupported('application/not-supported', 'Version: 1'));
    }
}
