# ~/.bashrc: executed by bash(1) for non-login shells.
# see /usr/share/doc/bash/examples/startup-files (in the package bash-doc)
# for examples

# If not running interactively, don't do anything
case $- in
    *i*) ;;
      *) return;;
esac

# don't put duplicate lines or lines starting with space in the history.
# See bash(1) for more options
HISTCONTROL=ignoreboth

# append to the history file, don't overwrite it
shopt -s histappend

# for setting history length see HISTSIZE and HISTFILESIZE in bash(1)
HISTSIZE=1000
HISTFILESIZE=2000

# check the window size after each command and, if necessary,
# update the values of LINES and COLUMNS.
shopt -s checkwinsize

# If set, the pattern "**" used in a pathname expansion context will
# match all files and zero or more directories and subdirectories.
#shopt -s globstar

# make less more friendly for non-text input files, see lesspipe(1)
[ -x /usr/bin/lesspipe ] && eval "$(SHELL=/bin/sh lesspipe)"

# set variable identifying the chroot you work in (used in the prompt below)
if [ -z "${debian_chroot:-}" ] && [ -r /etc/debian_chroot ]; then
    debian_chroot=$(cat /etc/debian_chroot)
fi

# set a fancy prompt (non-color, unless we know we "want" color)
case "$TERM" in
    xterm-color) color_prompt=yes;;
esac

# uncomment for a colored prompt, if the terminal has the capability; turned
# off by default to not distract the user: the focus in a terminal window
# should be on the output of commands, not on the prompt
force_color_prompt=yes

if [ -n "$force_color_prompt" ]; then
    if [ -x /usr/bin/tput ] && tput setaf 1 >&/dev/null; then
	# We have color support; assume it's compliant with Ecma-48
	# (ISO/IEC-6429). (Lack of such support is extremely rare, and such
	# a case would tend to support setf rather than setaf.)
	color_prompt=yes
    else
	color_prompt=
    fi
fi

if [ "$color_prompt" = yes ]; then
    PS1='${debian_chroot:+($debian_chroot)}\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ '
else
    PS1='${debian_chroot:+($debian_chroot)}\u@\h:\w\$ '
fi
unset color_prompt force_color_prompt

# If this is an xterm set the title to user@host:dir
case "$TERM" in
xterm*|rxvt*)
    PS1="\[\e]0;${debian_chroot:+($debian_chroot)}\u@\h: \w\a\]$PS1"
    ;;
*)
    ;;
esac

# enable color support of ls and also add handy aliases
if [ -x /usr/bin/dircolors ]; then
    test -r ~/.dircolors && eval "$(dircolors -b ~/.dircolors)" || eval "$(dircolors -b)"
    alias ls='ls --color=auto'
    #alias dir='dir --color=auto'
    #alias vdir='vdir --color=auto'

    alias grep='grep --color=auto'
    alias fgrep='fgrep --color=auto'
    alias egrep='egrep --color=auto'
fi

# some more ls aliases
alias ll='ls -alF'
alias la='ls -A'
alias l='ls -CF'

alias gt='git status'
alias gb='git branch'
alias glog='git log'

alias gadd='git add .'
alias gcommit='git commit -m'

alias gpull='git pull '
alias gpullm='git pull origin master'
alias gpulld='git pull origin develop'
alias gpushm='git push -u origin master'
alias gpushd='git push -u origin develop'

alias gck='git checkout'
alias gckm='git checkout master'
alias gckd='git checkout develop'

alias gm='git merge'
alias gmm='git merge master'
alias gmd='git merge develop'

alias chapps='sudo chmod -R 777 /applications/'
alias chweb='sudo chmod -R 777 /var/www/'

alias logrun='sudo tail -f nohup.out'

alias jsee='ps -ef | grep "java"'
alias jrun='nohup /usr/bin/java -jar'
alias jmov='cd /applications/pcw_mov_server_gps/'
alias judp='cd /applications/pcw_java/'
alias jtcp='cd /applications/pcw_tcp/'
alias jtcpt='cd /applications/pcw_gps_tracker/'
alias jloc='cd /applications/monitoreo/'
alias jjar='java -jar'

alias jrestart='sh /jobs/automatic_server_reset.sh'
alias tcprestart='sh /jobs/tcp_automatic_server_reset.sh'
alias jkill='killall -9 java'

alias pgps='cd /var/www/pcw_gps'
alias pgpsm='cd /var/www/pcw_mov'
alias ploc='cd /var/www/pcw_localization'
alias pmov='cd /var/www/pcw_mov_server_web'
alias pbeta='cd /var/www/pcw_mov_server_web_beta'
alias pdev='cd /var/www/pcw_mov_server_web_dev'
alias patios='cd /var/www/cdav_patios'
alias pstq='cd /var/www/pcw_stq'
alias pprm='cd /var/www/parking_manager'

alias art='php artisan'
alias lclear='php artisan cache:clear'
alias lconfig='php artisan config:cache'
alias lclean='php artisan cache:clear & php artisan config:cache & sudo chmod -R 777 /var/www/pcw_mov_server_web'

Docker alias

alias dart='docker exec -it stq.nginx php artisan'
alias dcomposer='docker exec -it stq.nginx composer'
alias ddocs='docker exec -it stq.nginx php artisan ide-helper:models'
alias dlclean='docker exec -it stq.nginx php artisan config:cache'
alias dnpm='docker exec -it stq.nginx npm'
alias docs='sudo php artisan ide-helper:models'
alias dphp='sudo docker exec -it stq.nginx php'


alias pgmain='cd /etc/postgresql/9.5/main/'
alias pgconf='nano /etc/postgresql/9.5/main/postgresql.conf'


# Add an "alert" alias for long running commands.  Use like so:
#   sleep 10; alert
alias alert='notify-send --urgency=low -i "$([ $? = 0 ] && echo terminal || echo error)" "$(history|tail -n1|sed -e '\''s/^\s*[0-9]\+\s*//;s/[;&|]\s*alert$//'\'')"'

# Alias definitions.
# You may want to put all your additions into a separate file like
# ~/.bash_aliases, instead of adding them here directly.
# See /usr/share/doc/bash-doc/examples in the bash-doc package.

if [ -f ~/.bash_aliases ]; then
    . ~/.bash_aliases
fi

# enable programmable completion features (you don't need to enable
# this, if it's already enabled in /etc/bash.bashrc and /etc/profile
# sources /etc/bash.bashrc).
if ! shopt -oq posix; then
  if [ -f /usr/share/bash-completion/bash_completion ]; then
    . /usr/share/bash-completion/bash_completion
  elif [ -f /etc/bash_completion ]; then
    . /etc/bash_completion
  fi
fi


parse_git_branch() {
  git branch 2> /dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/ (\1)/'
}

PS1='${debian_chroot:+($debian_chroot)}\[\033[1;33m\]\u\[\033[00m\]\[\033[1;37m\]@\[\033[00m\]\[\033[01;32m\]oveland\[\033[00m\]\a:\[\033[0;36m\]\W\[\033[00m\] \[\033[0;33m\]$(parse_git_branch)\[\033[00m\] \n$ '
