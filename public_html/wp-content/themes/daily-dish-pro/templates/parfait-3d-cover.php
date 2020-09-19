<style>
	.crv-3d-book.book-container {
		margin: 3rem 0;
		display: flex;
		align-items: center;
		justify-content: center;
		perspective: 700px;
	}

	.crv-3d-book .book {
		width: 300px;
		height: 415px;
		position: relative;
		transform-style: preserve-3d;
		transform: rotateY(-25deg);
		transition: 1s ease;
		animation: 1s ease 0s 1 crv-3d-book-init-animation;
	}

	.crv-3d-book .book:hover {
		transform: rotateY(-10deg);
	}

	.crv-3d-book .book> :first-child {
		position: absolute;
		top: 0;
		left: 0;
		background-color: white;
		width: 300px;
		height: 415px;
		transform: translateZ(12.5px);
		background-color: #ffffff;
		border-radius: 0 2px 2px 0;
		box-shadow: 1px 1px 5px #888;
		backface-visibility: hidden;
	}

	.crv-3d-book .book::before {
		position: absolute;
		content: " ";
		background-color: white;
		left: 0;
		top: 1px;
		width: 23px;
		height: 413px;
		transform: translateX(286.5px) rotateY(90deg);
		background: linear-gradient(90deg,
				#fff 0%,
				#f9f9f9 10%,
				#fff 20%,
				#f9f9f9 30%,
				#fff 40%,
				#f9f9f9 50%,
				#fff 60%,
				#f9f9f9 70%,
				#fff 80%,
				#f9f9f9 90%,
				#fff 100%);
	}

	.crv-3d-book .book::after {
		position: absolute;
		top: 0;
		left: 0;
		content: " ";
		width: 300px;
		height: 415px;
		transform: translateZ(-12.5px);
		background-color: #ffffff;
		border-radius: 0 2px 2px 0;
		box-shadow: -10px 0 50px #888;
	}

	.crv-3d-book .sticker {
		position: absolute;
		height: 10rem;
		top: 0;
		z-index: 1;
		filter: drop-shadow(0 0.5rem 1rem #888);
		--transform: translate(150px, -50%);
		transform: var(--transform);
		transition: transform 1s cubic-bezier(0.175, 0.885, 0.32, 1.275);
	}

	.crv-3d-book:hover .sticker {
		animation: crv-3d-book-wiggle 5s infinite;
	}

	@keyframes crv-3d-book-init-animation {
		0% { transform: rotateY(-10deg); }
		100% { transform: rotateY(-25deg); }
	}

	@keyframes crv-3d-book-wiggle {
		0% { transform: var(--transform) rotate(0deg); }
		3% { transform: var(--transform) rotate(3deg); }
		10% { transform: var(--transform) rotate(-7deg); }
		15% { transform: var(--transform) rotate(0deg); }
		100% { transform: var(--transform) rotate(0deg); }
	}
</style>

<a class="crv-3d-book book-container">
	<img class="sticker" src="<?php echo esc_url( CHILD_URL . '/images/sticker-free.svg' ); ?>" alt="Kostenlos Sticker">
	<div class="book">
		<img alt="Parfait E-Book Cover" src="<?php echo esc_url( CHILD_URL . '/images/parfait-cover.jpg' ); ?>" />
	</div>
</a>
