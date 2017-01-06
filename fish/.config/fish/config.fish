source ~/.config/fish/aliases.fish

# Completions
function make_completion --argument-names alias command
    echo "
    function __alias_completion_$alias
        set -l cmd (commandline -o)
        set -e cmd[1]
        complete -C\"$command \$cmd\"
    end
    " | .
    complete -c $alias -a "(__alias_completion_$alias)"
end

make_completion g 'git'

# Local prompt customization
set -e fish_greeting
set -gx PATH $PATH .config/composer/vendor/bin

set -gx ANDROID_HOME $HOME/Android/Sdk
set -gx PATH $PATH $ANDROID_HOME/tools $ANDROID_HOME/platform-tools
