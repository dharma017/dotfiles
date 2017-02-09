# Local prompt customization
set -e fish_greeting
set -gx PATH $PATH .config/composer/vendor/bin

set -gx ANDROID_HOME $HOME/Android/Sdk
set -gx PATH $PATH $ANDROID_HOME/tools $ANDROID_HOME/platform-tools

set -gx GOROOT $HOME/.local/share/umake/go/go-lang
set -gx PATH $PATH $GOROOT/bin

bash ~/.vocab
