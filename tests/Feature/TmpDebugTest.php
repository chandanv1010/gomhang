<?php

namespace Tests\Feature;

use Tests\TestCase;

class TmpDebugTest extends TestCase
{
    public function test_dump(): void
    {
        foreach (['/', '/dang-nhap.html', '/smart-watch.html', '/smart-watch.html?brand=apple'] as $url) {
            $r = $this->get($url);
            fwrite(STDERR, sprintf(
                "RESULT %-34s status=%d len=%d\n",
                $url,
                $r->getStatusCode(),
                strlen($r->getContent())
            ));
        }

        $this->assertTrue(true);
    }
}
