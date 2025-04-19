<?php

use PHPUnit\Framework\TestCase;

class RenderTest extends TestCase
{

    private static string $templateFile;
    private static string $templateContent = 'Template Content <?php $this->block("block"); ?>';
    private static string $blockFile;
    private static string $blockContent = 'Block Content';

    private static string $rootDirectory;

    public static function setUpBeforeClass(): void
    {
        self::$rootDirectory = sys_get_temp_dir() . '/';

        self::$templateFile = 'test_template.php';
        file_put_contents(self::$rootDirectory . self::$templateFile, self::$templateContent);

        self::$blockFile = 'test_block.php';
        file_put_contents(self::$rootDirectory . self::$blockFile, self::$blockContent);
    }

    public static function tearDownAfterClass(): void
    {
        if (file_exists(self::$rootDirectory . self::$templateFile)) {
            unlink(self::$rootDirectory . self::$templateFile);
        }
        if (file_exists(self::$rootDirectory . self::$blockFile)) {
            unlink(self::$rootDirectory . self::$blockFile);
        }
    }


    public function testCreateRenderWithoutRootDirectory()
    {
        $view = new \FFPerera\Cubo\View();
        $render = new \FFPerera\Cubo\Render($view);

        // Check if the root directory is set to the document root
        $this->assertEquals($_SERVER['DOCUMENT_ROOT'], $render->getRootDirectory());
    }

    public function testCreateRenderWithRootDirectory()
    {
        $customDirectory = '/path/to/custom/directory';
        $view = new \FFPerera\Cubo\View();
        $render = new \FFPerera\Cubo\Render($view, $customDirectory);

        // Check if the root directory is set to the custom directory
        $this->assertEquals($customDirectory, $render->getRootDirectory());
    }

    public function testSetAndGetAViewObject()
    {
        // test set and get view
        $view = new \FFPerera\Cubo\View();
        $render = new \FFPerera\Cubo\Render($view);
        $this->assertEquals($view, $render->getView());
    }

    public function testSend()
    {
        $view = new \FFPerera\Cubo\View();
        $view->setLayout(self::$templateFile);

        // Create a Render instance with the directory containing the template
        $render = new \FFPerera\Cubo\Render($view, self::$rootDirectory);
        // Capture the output of the send method
        ob_start();
        $render->send();
        $output = ob_get_clean();

        // Assert that the output matches the template content
        $this->assertEquals('Template Content ', $output);
    }

    public function testBlock()
    {
        $view = new \FFPerera\Cubo\View();
        $view->setLayout(self::$templateFile);
        $view->setTemplate('block', self::$blockFile);

        // Create a Render instance with the directory containing the template
        $render = new \FFPerera\Cubo\Render($view, self::$rootDirectory);
        // Capture the output of the send method
        ob_start();
        $render->send();
        $output = ob_get_clean();

        // Assert that the output matches the template content
        $this->assertEquals('Template Content Block Content', $output);
    }

    public function testRenderWithLayoutAndTemplate()
    {
        $view = new \FFPerera\Cubo\View();
        $view->setLayout(self::$templateFile);
        $view->setTemplate('block', self::$blockFile);

        $render = new \FFPerera\Cubo\Render($view, self::$rootDirectory);
        // Capture the output of the send method

        /**
         * @var \FFPerera\Cubo\Response $response
         */
        $response = $render->render();

        // Assert that the output matches the template content
        $this->assertEquals('Template Content Block Content', $response->getData());
    }
}
