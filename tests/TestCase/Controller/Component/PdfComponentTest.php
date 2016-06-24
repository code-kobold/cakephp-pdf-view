<?php

namespace Pdf\Test\TestCase\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Event\Event;

/**
 * Test class for \Pdf\Controller\Component\PdfComponent
 *
 * @author Ron Metten <ccct@code-kobold.de>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class PdfComponentTest extends TestCase
{

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * No PDF request
     *
     * @return void
     */
    public function testNoPdf()
    {
        $controller = new PdfComponentTestsController(new Request(), new Response());
        $controller->startupProcess();
        $this->assertFalse($controller->components()->Pdf->respondAsPdf);
    }

    /**
     * PDF request
     *
     * @return void
     */
    public function testPdf()
    {
        $request = new Request();
        $request->params = [
            'controller' => 'PdfComponentTests',
            'action' => 'renderPdf',
            '_ext' => 'pdf',
        ];

        $controller = new PdfComponentTestsController($request, new Response());
        $controller->startupProcess();

        $this->assertTrue($controller->components()->Pdf->respondAsPdf);
    }

    /**
     * Wrong extension as request parameter
     *
     * @return void
     */
    public function testPdfWrongExtension()
    {
        $request = new Request();
        $request->params = [
            'controller' => 'PdfComponentTests',
            'action' => 'renderPdf',
            '_ext' => 'pfd',
        ];

        $controller = new PdfComponentTestsController($request, new Response());
        $controller->startupProcess();

        $this->assertFalse($controller->components()->Pdf->respondAsPdf);
    }

    /**
     * Unset autodetect
     *
     * @return void
     */
    public function testSetAutoDetectFalse()
    {
        $request = new Request();
        $request->params = [
            'controller' => 'PdfComponentTests',
            'action' => 'renderPdf',
            '_ext' => 'pfd',
        ];

        $controller = new PdfComponentTestsController($request, new Response());

        $controller->components()->unload('Pdf');
        $controller->components()->load('Pdf.Pdf', ['autoDetect' => false]);

        $controller->startupProcess();
        $this->assertFalse($controller->components()->Pdf->respondAsPdf);
    }

    /**
     * Unset autodetect in config
     *
     * @return void
     */
    public function testAutoDetectSetFalseInConfig()
    {
        Configure::write('Pdf.autoDetect', false);

        $request = new Request();
        $request->params = [
            'controller' => 'PdfComponentTests',
            'action' => 'renderPdf',
            '_ext' => 'pfd',
        ];

        $controller = new PdfComponentTestsController($request, new Response());

        $controller->startupProcess();
        $this->assertFalse($controller->components()->Pdf->respondAsPdf);
    }

    /**
     * Probe viewvars
     *
     * @return void
     */
    public function testSetViewvars()
    {
        $request = new Request();
        $request->params = [
            'controller' => 'PdfComponentTests',
            'action' => 'renderPdf',
            '_ext' => 'pdf',
        ];

        $controller = new PdfComponentTestsController($request, new Response());
        $controller->startupProcess();

        $results = ['foo' => 42, 'name' => 'name1'];
        $controller->set(compact('results'));

        $this->assertNotEmpty($controller->viewVars);

        $resultsInView = $controller->viewVars['results'];
        $this->assertEquals($resultsInView, $results);
    }

    /**
     * Probe viewbuilder
     *
     * @return void
     */
    public function testViewbuilder()
    {
        $request = new Request();
        $request->params = [
            'controller' => 'PdfComponentTests',
            'action' => 'renderPdf',
            '_ext' => 'pdf',
        ];

        $controller = new PdfComponentTestsController($request, new Response());
        $controller->startupProcess();

        $event = new Event('Controller.beforeRender');
        $controller->components()->Pdf->beforeRender($event);

        $this->assertEquals('Pdf', $controller->viewBuilder()->className());
    }
}

/**
 * Class PdfComponentTestsController
 *
 * @package Pdf\Test\TestCase\Controller\Component
 */
class PdfComponentTestsController extends Controller
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Pdf.Pdf', ['viewClass' => 'Pdf', 'autoDetect' => true]);
        Request::addDetector('pdf', ['accept' => ['application/pdf'], 'param' => '_ext', 'value' => 'pdf']);
    }

    /**
     * Dummy method
     *
     * @return void
     */
    public function renderPdf()
    {

    }

}
