/* global __AppName__ */

(function () {
    function load_themes() {
        angular.module(__AppName__).config(function ($mdThemingProvider) {
            $mdThemingProvider.definePalette('principalTheme', {
                '50': 'cef4ff',
                '100': 'abdff9',
                '200': '8dc4e3',
                '300': '6aaacc',
                '400': '4f96ba',
                '500': '2d83a9',
                '600': '1b7497',
                '700': '006080',
                '800': '004d6a',
                '900': '003751',
                'A100': '2439be',
                'A200': '2f44cb',
                'A400': '5768dd',
                'A700': 'c6caf3',
                'contrastDefaultColor': 'light',
                'contrastDarkColors': ['50', '100', '200', '300', '400', 'A100'],
                'contrastLightColors': undefined
            });

            $mdThemingProvider.definePalette('privateTheme', {
                '50': 'e7efe4',
                '100': 'c3d6bb',
                '200': '9cbb8e',
                '300': '749f61',
                '400': '568b3f',
                '500': '38761d',
                '600': '326e1a',
                '700': '2b6315',
                '800': '245911',
                '900': '17460a',
                'A100': '94ff7d',
                'A200': '6aff4a',
                'A400': '40ff17',
                'A700': '2dfc00',
                'contrastDefaultColor': 'light',
                'contrastDarkColors': [
                    '50',
                    '100',
                    '200',
                    '300',
                    'A100',
                    'A200',
                    'A400',
                    'A700'
                ],
                'contrastLightColors': [
                    '400',
                    '500',
                    '600',
                    '700',
                    '800',
                    '900'
                ]
            });

            $mdThemingProvider.theme('topbar')
                    .primaryPalette('principalTheme', {
                        'default': '600',
                        'hue-1': 'A100'
                    })
                    .accentPalette('principalTheme', {
                        'default': 'A200'
                    }).dark();
                    
            $mdThemingProvider.theme('topbar_private')
                    .primaryPalette('privateTheme', {
                        'default': '600',
                        'hue-1': 'A100'
                    })
                    .accentPalette('privateTheme', {
                        'default': 'A200'
                    }).dark();

            $mdThemingProvider.theme('principal')
                    .primaryPalette('principalTheme', {
                        'default': '600',
                        'hue-1': '50'
                    })
                    .accentPalette('principalTheme', {
                        'default': '600'
                    });
                    
            $mdThemingProvider.theme('private')
                    .primaryPalette('privateTheme', {
                        'default': '600',
                        'hue-1': '50'
                    })
                    .accentPalette('privateTheme', {
                        'default': '600'
                    });
//            $mdThemingProvider.theme('principal').dark();

        });
    }
    if (angular) {
        load_themes();
    }
})();