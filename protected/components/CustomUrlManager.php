<?php
/**********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 *    version                :    1.3.2
 *    copyright            :    (c) 2012 Monoray
 *    website                :    http://www.monoray.ru/
 *    contact us            :    http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Real Estate
 *
 * Open Real Estate is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 ***********************************************************************************************/
class CustomUrlManager extends CUrlManager {

    private $_myRules = array();
    private $_replaceSymb = array(',', '/', '!', '#', '~', '@', '%', '^', '&', '?', '/', '\\', '|', '-', '+', '.', ' ', '--');

    public function parseMyInitRules()
    {
        if (!$this->_myRules) {
            return;
        }
        foreach ($this->_myRules as $rule) {
            $seoPattern = $this->parseMyLink($rule['replace'], $rule['pattern']);

            if ($seoPattern) {
                $this->rules[$seoPattern] = $rule['route'];
                $this->addRules(array($seoPattern => $rule['route']));
            }
        }
    }

    public function createUrl($route, $params = array(), $ampersand = '&')
    {
        if ($this->_replaceSymb && isset($params['title'])) {
            $params['title'] = str_replace($this->_replaceSymb, '-', $params['title']);
        }

        $return = $this->parseMyRules($params, $ampersand);
        if ($return) {
            return $return;
        }


        return parent::createUrl($route, $params, $ampersand);
    }

    private function parseMyLink($replaceTo = array(), $seoPattern = '')
    {
        if ($replaceTo) {
            if ($seoPattern) {
                $seoPattern = str_replace(array(
                    '::title',
                    '::id',
                    '::text',
					'::continent',
					'::state',
					'::region',
					'::station',
                ), $replaceTo, $seoPattern);
                return $seoPattern;
            }
        }
        return false;
    }

    public function parseMyRules($params, $ampersand)
    {
        if (!$this->_myRules) {
            return;
        }
        if (isset($params['id']) && isset($params['title'])) {
            foreach ($this->_myRules as $rule) {
                if (Yii::app()->controller->route == $rule['route']) {
                    if ($this->_replaceSymb) {
                        $params['title'] = str_replace($this->_replaceSymb, '-', $params['title']);
                    }
                }
            }
        }
        return false;
    }
}