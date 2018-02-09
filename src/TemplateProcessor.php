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
 
class TemplateProcessor
{
    private $config;
    private $options;
    private $path = __DIR__ . '/';
    private $loader;
    private $response;
    private $render;
    private $view;
    private $processor;
    private $driver;
    private $template;
    private $install = null;
 
    public function __construct($config = [], $template = null)
    {
        // Подключаем конфиг из конструктора
        if(isset($config)) {
            $this->config = $config;
        }
        // Подключаем конфиг из конструктора
        if(isset($template)) {
            $this->template = $template;
        }
        // Подключаем конфиг из конструктора
        if(isset($config['settings']["install"]["status"])) {
            $this->install = $config['settings']["install"]["status"];
        }
        // Получаем название шаблонизатора
        if (isset($this->config['template']['front_end']['processor'])) {
            $this->processor = $this->config['template']['front_end']['processor'];
        } else {
            $this->processor = 'twig';
        }
        $this->driver();
    }
 
    public function render($response, $render = null, $view = [])
    {
        $this->response = $response;
        if(isset($render)) {
            $this->render = $render;
        }
        if(isset($view)) {
            $this->view = $view;
        }
        $processor = strtolower($this->processor);
        if ($processor == 'twig') {
            return $this->driver->render($this->render, $this->view);
        } elseif ($processor == 'blade') {
            return null;
        } elseif ($processor == 'smarty') {
            return null;
        } elseif ($processor == 'mustache') {
            return null;
        } elseif ($processor == 'phprenderer') {
            return null;
        } else {
            return null;
        }
    }

    public function driver()
    {
        $themes = $this->config['template']['front_end']['themes'];
        $processor = strtolower($this->processor);
        $cache = false;
        $strict_variables = false;
 
        if ($this->install != null) {
            if ($processor == 'twig') {
                if (isset($this->config['cache']['twig']['state'])) {
                    if ((int)$this->config['cache']['twig']['state'] == 1) {
                        $cache = __DIR__ .''.$this->config['cache']['twig']['cache_dir'];
                        $strict_variables = $this->config['cache']['twig']['strict_variables'];
                    }
                }
                $loader = new \Twig_Loader_Filesystem($this->config["settings"]["themes"]["front_end_dir"]."/".$themes['templates']."/".$this->template."/layouts");
                $this->driver = new \Twig_Environment($loader, ['cache' => $cache, 'strict_variables' => $strict_variables]);
            } elseif ($processor == 'blade') {
                $this->driver = null;
            } elseif ($processor == 'smarty') {
                $this->driver = null;
            } elseif ($processor == 'mustache') {
                $this->driver = null;
            } elseif ($processor == 'phprenderer') {
                $this->driver = null;
            } else {
                $this->driver = null;
            }
        } else {
            $loader = new \Twig_Loader_Filesystem($this->config["settings"]["themes"]["front_end_dir"]."/".$themes['templates']."/install");
            $this->driver = new \Twig_Environment($loader, ['cache' => false, 'strict_variables' => false]);
        }
 
        return $this->driver;
 
    }
 
    public function set_path($path = null)
    {
        if(isset($path)) {
            $this->path = $path;
        }
        
    }

}
 