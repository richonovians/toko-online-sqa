<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;

class SystemTest extends TestCase{
    private $driver;
    private $baseUrl = 'http://localhost:8000';

    protected function setUp(): void{
        $host = 'http://localhost:9515/wd/hub';

        $chromeOptions = new ChromeOptions();
        $chromeOptions->addArguments([
            '--headless',
            '--disable-gpu',
            '--no-sandbox'
        ]);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(
            ChromeOptions::CAPABILITY,
            $chromeOptions
        );

        $this->driver = RemoteWebDriver::create($host, $capabilities);
    }

    public function testHomepageAndSearchFeature(){
        // Kunjungi server lokal
        $this->driver->get($this->baseUrl);

        // Validasi antarmuka memuat teks judul
        $bodyText = $this->driver->findElement(WebDriverBy::tagName('body'))->getText();
        $this->assertStringContainsString('Toko Online', $bodyText);

        // Simulasi pengguna mencari barang
        $searchBox = $this->driver->findElement(WebDriverBy::name('cari'));
        $searchBox->sendKeys('Kemeja');
        $searchBox->submit(); // Tekan enter

        // Validasi hasil pencarian
        $updatedBodyText = $this->driver->findElement(WebDriverBy::tagName('body'))->getText();
        $this->assertStringContainsString('Kemeja Flanel', $updatedBodyText);
    }

    protected function tearDown(): void{
        if ($this->driver) {
            $this->driver->quit(); // Tutup browser
        }
    }
}