# Quick edits
alias ea 'vim ~/.config/fish/aliases.fish'
alias ef 'vim ~/.config/fish/config.fish'
alias eg 'vim ~/.gitconfig'
alias ev 'vim ~/.vimrc'
alias es 'vim ~/bin/autosort'
alias et 'vim ~/.tmux.conf'

alias g git
alias c clear
alias v vim
alias n nvim
alias k gitk

alias vim-norc 'vim -u NORC'
alias vim-none 'vim -u NONE'

# Navigation
function ..    ; cd .. ; end
function ...   ; cd ../.. ; end
function ....  ; cd ../../.. ; end
function ..... ; cd ../../../.. ; end

# Networking. IP address, dig, DNS
alias ip="dig +short myip.opendns.com @resolver1.opendns.com"
alias dig="dig +nocmd any +multiline +noall +answer"

alias df 'command df -m'
alias j jobs
alias l ls
alias ll 'ls -la'
alias ls 'command ls -FG'
alias su 'command su -m'
alias md 'mkdir -p'

function serve
    if test (count $argv) -ge 1
        if python -c 'import sys; sys.exit(sys.version_info[0] != 3)'
            /bin/sh -c "(cd $argv[1] && python -m http.server)"
        else
            /bin/sh -c "(cd $argv[1] && python -m SimpleHTTPServer)"
        end
    else
        python -m SimpleHTTPServer
    end
end

function lsd -d 'List only directories (in the current dir)'
    command ls -d */ | sed -Ee 's,/+$,,'
end


# Git
alias nah="git reset --hard;git clean -df"
alias master="git checkout master"
alias gst="git status"
alias gss="git status -s"
alias ggpull="git pull origin (git rev-parse --abbrev-ref HEAD)"
alias ggpush="git push origin (git rev-parse --abbrev-ref HEAD)"
alias ga="git add"
alias gaa="git add --all"
alias gb="git branch"
alias gba="git branch -a"
alias gc="git commit -v"
alias gca="git commit -a"
alias gcl="git config --list"
alias gclean="git clean -fd"
alias gcm="git checkout master"
alias gco="git checkout"
alias gd="git diff"
alias gfa="git fetch --all --prune"
alias ggpnp="git pull origin (git rev-parse --abbrev-ref HEAD) ; git push origin (git rev-parse --abbrev-ref HEAD)"
alias gm="git merge"
alias gr="git remote"
alias grv="git remote -v"
alias glg="git log --stat --max-count=10"

# Ubuntu terminal commands
alias install="sudo apt-get install -y"
alias update="sudo apt-get update"
alias upgrade="sudo apt-get update ; sudo apt-get upgrade"

# Youtube-dl
alias mp3="youtube-dl --extract-audio --audio-format mp3"
alias bestav="youtube-dl -f bestvideo+bestaudio"
alias mp4="youtube-dl -f 135+140"
alias bestmp4="youtube-dl -f bestvideo[ext=mp4]+bestaudio[ext=m4a]/mp4"
alias playlist="youtube-dl -citw -f 135+140 --no-post-overwrites"
alias bestplaylist="youtube-dl -citw -f bestvideo[height<=480]+bestaudio/best[height<=480]"
