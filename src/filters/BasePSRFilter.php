<?hh // strict
/*
 *  Copyright (c) 2015-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the BSD-style license found in the
 *  LICENSE file in the root directory of this source tree. An additional grant
 *  of patent rights can be found in the PATENTS file in the same directory.
 *
 */

namespace Facebook\AutoloadMap;

abstract class BasePSRFilter implements Builder {

  abstract protected static function getExpectedPath(
    string $classname,
    string $prefix,
    string $root,
  ): string ;

  final public function __construct(
    private string $prefix,
    private string $root,
    private Builder $source,
  ) {
  }

  public function getFiles(): ImmVector<string> {
    return ImmVector { };
  }

  public function getAutoloadMap(): AutoloadMap {
    $classes =
      (new Map($this->source->getAutoloadMap()['class']))
      ->filterWithKey(
        function(string $class_name, string $file): bool {
          if (stripos($class_name, $this->prefix) !== 0) {
            return false;
          }
          $expected = static::getExpectedPath(
            $class_name,
            $this->prefix,
            $this->root,
          );
          return strtolower($expected) === strtolower($file);
        }
      );

    return shape(
      'class' => $classes->toArray(),
      'function' => [],
      'type' => [],
      'constant' => [],
    );
  }
}
