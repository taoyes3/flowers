<?php

return [
    'alipay' => [
        'app_id' => '2016091900547972',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3ac9RsVGCyK2Pz5SYXTiiuS+BE5b3Mn5Yh2z/mNIUIiPHdVhqZ1u2sgvzrn1UY1WXl9EVVmyF1GrHLRNcneM0ZfPKEO6WJ+PNA7J1axYU4Asr2kYVnkeXlgDEuxuZ7P+QH04xW+DFwanWDTPtK+QcZnjCte/ZP8wtJLwgu7NgHI80vuFC9FR9gG0amNZZna8VWmJUjMPyy0Fo/+touHTQ0NkHTvwDBLdv92252UyXIWMbdUeBZ/igzigjBIOQ7K61gUmY/bxI+tAsrxvFtZZfLkQATyYwjosmhKfz/pzTlOiA15v0IhcVCNkZo640Mie6QyElpAeuS157YvJUhij7wIDAQAB',
        'private_key' => 'MIIEowIBAAKCAQEAxPDH+0HAst/fEUgmt1adX2N8uVbRuwHV+DJQqPK9n2mIH1QLxghxEZnIYCV17pp/yzrTRgoMrgl3DKhr5Mb2F7beRqYBD12Dw5SA3UFg7rcxrjihNm0cCnr75iQbBODNxaPS6cTXAJzUXL6v0eWWjkjngKuMJtkQQrzr0BYx79s0Fgygy9AH/z6/M2q49P3j1sA+raa5HlFo+u3DauLIH2sTjrdj1KmpR8rSPIqe8Y0Oe0q0pSpR90R9+/YnTuPRUl8trN0DB99eRGMstpC6npp/Q9gyhtONztM/smCWeuGi7O0kxtT28QfM+D5EJOGPnqg3rER6PEvfnb+1NCiSuQIDAQABAoIBACpiByezhtz4APGfLXXXVhbpOjZI0OoGAx5e9azmUJi2BW14kPAVcP/wsIbAiRjIdygtiLpR/1iVAOHgfRfHGb8Qw9VAdInx/iFhHDqBj3LQSgjMdyVXZel7fiqRKp9qId6e2w0kJ4EiPzGWeBAb8MK+PdQbFxZlMHKsZbNqk2SizsAzEcX9N+g6Ppn+nDQUUMHaYb+1MqkqKXdku/1plwJTbh61N+cgYstDMnG/FtkM+i4Mo3EqJqcG6bchYr9LnREYg4WFDplj0DDljBoJS+Num8/jn93r10HC4N1ix/2m/6C/AgNZoGf7Mm0M8Ap3DBtW8rI1m0EXSRGY8txXPAECgYEA/W2m1wh7eZo8h9llSfIDy902KLXLFG/9naWQ7y+qSxrcVfxWbGzPdG7K8hfKrdA6A5Ot8xSwVXbjaQ4CrUA+BYyRpa5l2C2ufT3P6C22L8XGtlu7fR49LJSJKmsBiurgCtZSzhQ0xFMZoaqGtIxwOmgW+PjkLpcRxslSyhPrahECgYEAxvBjI3tTbskplqz8LFASi7zuW8Zci0ypINUIQdLcWMpJCHW2X5XgtLV100aOIAeNpckj6rKk02LyRnsJOUhTGBDrNwERl8kobaVT3eF6KZ4eSisQFH24aNJtd0nrN7KVdtcsE5G+L/lYasErDaVh94LkDHAjykoi0x1IY/zFNikCgYEAhjbQKQmg+uGoNPn5HpRBVnItAJpmlshItHi+rS29LlFnZ1JgIsPtimgyqsvW1v5z+vj3IslOKbTw8u4slLx1HFM5v1kBLt/ijQlzVi7/UYWYm3MxdC3TkAyKKCDLjFqflJ209NbWqzKnXGsHNA88UHJX2DtX/SSckX6FjefGwUECgYBACMpwdlbhtyCK3n2BKfj+P78djVjmgbOC3O9+eIfykJI3heRBGjYtFKerbO98gdHnRjooZn5FiHjhlOLgLFaKzY9YsiBekiJQQMIhDl3LHZk0WG4hmmIMY4dWuVVcJUCU8ye92NC2EnWST0EzcEN7bwdGtaXfjENvXuKZRJiaGQKBgHA8OKFDwOvhvYD7Q4twRYWrdysXDaO7UTFJ9+1Mhm/8hQKxw+6fnLBGraRSbAkrZNf6kJbXoaH50Usxpk6iktshakZqHvlBf+6si4fRwZz/q+Jq0rpnj7SetkupqRonDkKYLfRvigfY4lDO9qOW14A9PZSS2e8Y14l1/ltECH5J',
        'log' => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],
    'wechat' => [
        'app_id' => '',
        'mch_id' => '',
        'key' => '',
        'cert_client' => '',
        'cert_key' => '',
        'log' => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];