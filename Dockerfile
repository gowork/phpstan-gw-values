FROM ubuntu:18.04
ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && apt-get install -y software-properties-common locales

RUN locale-gen en_US.UTF-8
ENV LANG=en_US.UTF-8

RUN apt-add-repository ppa:git-core/ppa -y && \
    apt-get update && \
    apt-get install -y --force-yes \
	    nano \
	    git \
	    curl \
	    vim \
	    wget \
        zsh \
        iputils-ping \
        gnupg-agent \
        rsync

RUN useradd -ms /bin/zsh gowork

WORKDIR /home/gowork

RUN apt-add-repository -y ppa:ondrej/php && apt-get update && apt-get install -y --force-yes \
	php7.3-curl \
	php7.3-gd \
	php7.3-intl \
	php7.3-mysql \
	php7.3-xml \
	php7.3-mbstring \
	php7.3-bcmath \
    php7.3-mongo \
	php7.3-zip \
	php7.3-opcache \
	php7.3-bz2 \
	php7.3-gmp \
	php7.3-redis \
    php7.3-cli

RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

USER gowork

RUN git clone git://github.com/robbyrussell/oh-my-zsh.git /home/gowork/.oh-my-zsh \
      && cp /home/gowork/.oh-my-zsh/templates/zshrc.zsh-template /home/gowork/.zshrc \
      && sed -i.bak 's/robbyrussell/nebirhos/' /home/gowork/.zshrc

RUN echo "plugins=(git symfony symfony2 composer yarn)" >> /home/gowork/.zshrc

# from https://hub.docker.com/r/themattrix/develop/~/dockerfile/
RUN git clone https://github.com/junegunn/fzf.git /home/gowork/.fzf \
    && (cd /home/gowork/.fzf) \
    && (yes | /home/gowork/.fzf/install)

RUN mkdir /home/gowork/.ssh && \
	echo "StrictHostKeyChecking no\n" >> /home/gowork/.ssh/config

RUN echo "export FZF_DEFAULT_OPTS='--no-height --no-reverse'" >> /home/gowork/.zshrc

RUN mkdir /home/gowork/current
RUN touch /home/gowork/.zsh_history
WORKDIR /home/gowork/current
