<?php
/**
 * This file is part of the API SHOP
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/pllano/Adapter
 * @version 1.0.1
 * @package pllano.cache
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Pllano\Adapter;
 
use Psr\Http\Message\ResponseInterface as Response;
use Pllano\Adapter\PhpRenderer;
 
class TemplateEngine
{
    private $config;
    protected $options;
    protected $loader;
    protected $response;
    protected $render;
    protected $view;
    protected $template_engine;
    protected $renderer;
    protected $template;
    protected $install = null;
 
    public function __construct($config = [], $template = null)
    {
        // Подключаем конфиг из конструктора
        if(isset($config)) {
            $this->config = $config;
        }
        if(isset($template)) {
            $this->template = $template;
        } else {
		    $this->template = $this->config['template']['front_end']['themes']['template'];
		}
        // Подключаем конфиг из конструктора
        if(isset($config['settings']["install"]["status"])) {
            $this->install = $config['settings']["install"]["status"];
        }
        // Получаем название шаблонизатора
        if (isset($this->config['template']['front_end']['template_engine'])) {
            $this->template_engine = $this->config['template']['front_end']['template_engine'];
        } else {
            $this->template_engine = 'twig';
        }
 
        $this->renderer();
    }
 
    public function render(Response $response, $render = null, $view = [])
    {
        $this->response = $response;
        if(isset($render)) {
            $this->render = $render;
        }
        if(isset($view)) {
            $this->view = $view;
        }
        $template_engine = strtolower($this->template_engine);
		if ($this->install != null) {
            if ($template_engine == 'twig') {
                return $this->renderer->render($this->render, $this->view);
            } elseif ($template_engine == 'blade') {
                return null;
            } elseif ($template_engine == 'smarty') {
                return null;
            } elseif ($template_engine == 'mustache') {
                return null;
            } elseif ($template_engine == 'phprenderer') {
                return $this->renderer->render($this->response, $this->render, $this->view);
            } else {
                return null;
            }
		} else {
		    return $this->renderer->render($this->render, $this->view);
		}
    }

    public function renderer()
    {
        $themes = $this->config['template']['front_end']['themes'];
        $template_engine = strtolower($this->template_engine);
        $cache = false;
        $strict_variables = false;
		
		$layouts = $this->config["settings"]["themes"]["front_end_dir"]."/".$themes['templates']."/".$this->template."/layouts";
 
        if ($this->install != null) {
            if ($template_engine == 'twig') {
                if (isset($this->config['cache']['twig']['state'])) {
                    if ((int)$this->config['cache']['twig']['state'] == 1) {
                        $cache = __DIR__ .''.$this->config['cache']['twig']['cache_dir'];
                        $strict_variables = $this->config['cache']['twig']['strict_variables'];
                    }
                }
                $loader = new \Twig_Loader_Filesystem($layouts);
                $this->renderer = new \Twig_Environment($loader, ['cache' => $cache, 'strict_variables' => $strict_variables]);
            } elseif ($template_engine == 'blade') {
                $this->renderer = null;
            } elseif ($template_engine == 'smarty') {
                $this->renderer = null;
            } elseif ($template_engine == 'mustache') {
                $this->renderer = null;
            } elseif ($template_engine == 'phprenderer') {
                $this->renderer = new PhpRenderer($layouts);
            } else {
                $this->renderer = null;
            }
        } else {
            $loader = new \Twig_Loader_Filesystem($this->config["settings"]["themes"]["front_end_dir"]."/".$themes['templates']."/install");
            $this->renderer = new \Twig_Environment($loader, ['cache' => false, 'strict_variables' => false]);
        }
 
        return $this->renderer;
 
    }
 
}
 