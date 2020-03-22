<?php

namespace Vayes\Config\Provider;

use Vayes\Exception\InvalidArgumentException;
use Vayes\Exception\InvalidOptionException;

class ConfigProvider
{
    /**
     * All below usages are allowed:
     *
     *   Single key
     * - Config::item('level1')
     *
     *   Key with segment (Recommended for best practice)
     * - Config::item('level1-1', 'level1-0')
     * - Config::item('level2-1.level2-2', 'level2-0')
     * - Config::item('level3-1.level3-2.level3-3', 'level3-0')
     *
     *   Keys with dot notation
     * - Config::item('level1-0.level1-1')
     * - Config::item('level2-0.level2-1.level2-2')
     * - Config::item('level3-0.level3-1.level3-2.level3-3')
     *
     * @param string      $item
     * @param string|null $segment
     * @return mixed|null
     */
    public function item(string $item, ?string $segment = null)
    {
        if (defined('CI_VERSION')) {
            if (null === $segment) {
                return $this->itemResolver($item);
            } else {
                return $this->itemResolver($item, $segment);
            }
        }

        throw new InvalidArgumentException('This method works for CodeIgniter 3 only.');
    }

    /**
     * @param string      $item
     * @param string|null $segment
     * @return mixed|null
     */
    private function itemResolver(string $item, ?string $segment = null)
    {
        $val = null;

        if (null === $segment) {
            $val = $this->getItem($item);
            if (null === $val && true === (bool)stripos($item, '.')) {
                $val = null;
                $itAr = explode('.', $item);
                foreach ($itAr as $subIt) {
                    if (null === $val) {
                        $val = $this->getItem($subIt);
                        if (null === $val) {
                            break;
                        } else {
                            continue;
                        }
                    }

                    if (true === empty($val[$subIt])) {
                        $val = null;
                        break;
                    }

                    $val = $val[$subIt];
                }
            }
        } else {
            $cfgVal = $this->getItem($segment);

            if (null === $cfgVal) {
                throw new InvalidOptionException(sprintf(
                    'Config array has no segment named \'%s\'.',
                    $segment
                ));
            }

            if (isset($cfgVal[$item])) {
                return $cfgVal[$item];
            } else {
                if (true === (bool)stripos($item, '.')) {
                    $itAr = explode('.', $item);

                    foreach ($itAr as $subIt) {
                        if (true === empty($cfgVal[$subIt])) {
                            $cfgVal = null;
                            break;
                        }

                        $cfgVal = $cfgVal[$subIt];
                    }

                    if (null === $cfgVal) {
                        $val = null;
                    } else {
                        $val = $cfgVal;
                    }
                }
            }
        }

        return $val;
    }

    private function getItem($configItem)
    {
        return config_item($configItem);
    }
}
