<?php

/**********************************************************************************************************************
 * phpLINQ (https://github.com/mkloubert/phpLINQ)                                                                     *
 *                                                                                                                    *
 * Copyright (c) 2015, Marcel Joachim Kloubert <marcel.kloubert@gmx.net>                                              *
 * All rights reserved.                                                                                               *
 *                                                                                                                    *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the   *
 * following conditions are met:                                                                                      *
 *                                                                                                                    *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the          *
 *    following disclaimer.                                                                                           *
 *                                                                                                                    *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the       *
 *    following disclaimer in the documentation and/or other materials provided with the distribution.                *
 *                                                                                                                    *
 * 3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote    *
 *    products derived from this software without specific prior written permission.                                  *
 *                                                                                                                    *
 *                                                                                                                    *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, *
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE  *
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, *
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR    *
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,  *
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE   *
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                                           *
 *                                                                                                                    *
 **********************************************************************************************************************/

namespace phpLINQ\Docs;


trait OutputDirectoryHandler {
    use BaseDirectoryHandler;


    protected function copyFile($file, $dest, bool $overwrite = true) {
        $baseDir = $this->baseDir();
        if (false === $baseDir) {
            return false;
        }

        $outDir = $this->outDir();
        if (false === $outDir) {
            return false;
        }

        $file = \realpath($baseDir . \DIRECTORY_SEPARATOR . $file);
        if (false === $file) {
            return $file;
        }

        if (!\is_file($file)) {
            return false;
        }

        $dest = $this->createDirectory($dest);
        if (false === $dest) {
            return false;
        }

        $targetFile = $dest . \DIRECTORY_SEPARATOR . \basename($file);

        if (!$overwrite) {
            if (\file_exists($targetFile)) {
                return null;
            }
        }

        return \copy($file, $targetFile);
    }

    protected function createDirectory($dest) {
        $outDir = $this->outDir();
        if (false === $outDir) {
            return false;
        }

        $dest = $outDir . \DIRECTORY_SEPARATOR . $dest;
        if (!\file_exists($dest)) {
            if (!mkdir($dest, 0777, true)) {
                return false;
            }
        }

        $dest = \realpath($dest);

        if (false === $dest) {
            return false;
        }

        if (!\is_dir($dest)) {
            return false;
        }

        return $dest;
    }

    protected function createNewFile($file, $overwrite = true) {
        $outDir = $this->outDir();
        if (false === $outDir) {
            return false;
        }

        $file = $outDir . DIRECTORY_SEPARATOR . $file;
        if (\file_exists($file)) {
            if (!$overwrite) {
                return null;
            }
        }

        $dir = \dirname($file);
        if (false === $dir) {
            return false;
        }

        if (!\file_exists($dir)) {
            if (!mkdir($dir, 0777, true)) {
                return false;
            }

            $dir = \realpath($dir);
        }

        if (false === $dir) {
            return false;
        }

        return \fopen($file, 'w+');
    }

    /**
     * @return string
     */
    abstract public function outDir();
}
