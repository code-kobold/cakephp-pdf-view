<?php

namespace Pdf\Test\TestCase\View;

use Cake\Controller\Controller;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;
use Pdf\View\PdfView;
use Cake\Core\Configure;

/**
 * Testclass for \Pdf\View\PdfView
 *
 * @author Ron Metten <ccct@code-kobold.de>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class PdfViewTest extends TestCase {

    protected $foo = 3.14159265;

    protected $bar = 'baz';

	/**
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * @return void
	 */
	public function testRender() {

		$request = new Request();
		$response = new Response();
        $eventManager = new \Cake\Event\EventManager();

        $viewOptions = [
            'ext' => 'xctp',
            'templatePath' => 'PdfComponentTests',
            'template' => 'render_pdf',
        ];

        $view = new PdfView($request, $response, $eventManager, $viewOptions);
        $view->viewPath = 'PdfComponentTests';

        $view->set('foo', $this->foo);
        $view->set('bar', $this->bar);

        $result = $view->render('renderPdf');

        $this->assertContains(
            (string)$this->foo,
            $result
        );

        $this->assertContains(
            $this->bar,
            $result
        );
	}

	public function testRenderFromController() {

		$request = new Request();
		$response = new Response();
		$controller = new PdfComponentTestsController($request, $response);

		$controller->viewBuilder()->className('Pdf.Pdf');
		$controller->viewBuilder()->template('render_pdf');
		$controller->viewBuilder()->templatePath('PdfComponentTests');
		$controller->renderPdf();

		$result = $controller->render();
        $this->assertContains(
            (string)$this->foo,
            $result->body()
        );

        $this->assertContains(
            $this->bar,
            $result->body()
        );
	}
}


class PdfComponentTestsController extends Controller {

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Pdf.Pdf', ['viewClass' => 'Pdf', 'autoDetect' => true]);
        Request::addDetector('pdf', ['accept' => ['application/pdf'], 'param' => '_ext', 'value' => 'pdf']);
    }

    /**
     * @return void
     */
    public function renderPdf() {
        $foo = 3.14159265;
        $bar = 'baz';

        $this->set('foo', $foo);
        $this->set('bar', $bar);
    }

}
